<?php

namespace spolny\ArmaReforgerWorkshop\Filament\Server\Pages;

use App\Filament\Server\Resources\Files\Pages\ListFiles;
use App\Models\Server;
use App\Repositories\Daemon\DaemonFileRepository;
use App\Traits\Filament\BlockAccessInConflict;
use Exception;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;
use spolny\ArmaReforgerWorkshop\Facades\ArmaReforgerWorkshop;
use spolny\ArmaReforgerWorkshop\Services\ArmaReforgerWorkshopService;

class ArmaReforgerWorkshopPage extends Page implements HasTable
{
    use BlockAccessInConflict;
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-packages';

    protected static ?string $slug = 'workshop';

    protected static ?int $navigationSort = 30;

    public static function canAccess(): bool
    {
        /** @var Server $server */
        $server = Filament::getTenant();

        if (!$server) {
            return false;
        }

        return parent::canAccess() && ArmaReforgerWorkshop::isArmaReforgerServer($server);
    }

    public static function getNavigationLabel(): string
    {
        return 'Workshop Mods';
    }

    public static function getModelLabel(): string
    {
        return static::getNavigationLabel();
    }

    public static function getPluralModelLabel(): string
    {
        return static::getNavigationLabel();
    }

    public function getTitle(): string
    {
        return static::getNavigationLabel();
    }

    /**
     * @throws Exception
     */
    public function table(Table $table): Table
    {
        return $table
            ->records(function (?string $search, int $page) {
                /** @var Server $server */
                $server = Filament::getTenant();

                /** @var DaemonFileRepository $fileRepository */
                $fileRepository = app(DaemonFileRepository::class);

                $mods = ArmaReforgerWorkshop::getInstalledMods($server, $fileRepository);

                // Enrich with workshop details using concurrent requests for non-cached mods
                $enrichedMods = $this->enrichModsWithDetails($mods);

                // Apply client-side search filtering
                if ($search) {
                    $searchLower = strtolower($search);
                    $enrichedMods = array_filter($enrichedMods, function ($mod) use ($searchLower) {
                        return str_contains(strtolower($mod['name'] ?? ''), $searchLower)
                            || str_contains(strtolower($mod['modId'] ?? ''), $searchLower);
                    });
                    $enrichedMods = array_values($enrichedMods);
                }

                $perPage = 20;
                $offset = ($page - 1) * $perPage;

                return new LengthAwarePaginator(
                    array_slice($enrichedMods, $offset, $perPage),
                    count($enrichedMods),
                    $perPage,
                    $page
                );
            })
            ->paginated([20])
            ->columns([
                TextColumn::make('name')
                    ->label('Mod Name')
                    ->searchable()
                    ->description(fn (array $record) => $record['modId']),
                TextColumn::make('version')
                    ->label('Version')
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('subscribers')
                    ->label('Subscribers')
                    ->icon('tabler-users')
                    ->numeric()
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('downloads')
                    ->label('Downloads')
                    ->icon('tabler-download')
                    ->numeric()
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('rating')
                    ->label('Rating')
                    ->formatStateUsing(fn ($state) => $state ? "{$state}%" : '-')
                    ->toggleable(),
            ])
            ->recordUrl(fn (array $record) => ArmaReforgerWorkshop::getModWorkshopUrl($record['modId']), true)
            ->recordActions([
                Action::make('remove')
                    ->label('Remove')
                    ->icon('tabler-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Remove Mod')
                    ->modalDescription(fn (array $record) => "Are you sure you want to remove \"{$record['name']}\" from your server's mod list?")
                    ->action(function (array $record, DaemonFileRepository $fileRepository) {
                        try {
                            /** @var Server $server */
                            $server = Filament::getTenant();

                            $success = ArmaReforgerWorkshop::removeMod(
                                $server,
                                $fileRepository,
                                $record['modId']
                            );

                            if ($success) {
                                Notification::make()
                                    ->title(__('arma-reforger-workshop::arma-reforger-workshop.notifications.mod_removed'))
                                    ->body(__('arma-reforger-workshop::arma-reforger-workshop.notifications.mod_removed_body', ['name' => $record['name']]))
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title(__('arma-reforger-workshop::arma-reforger-workshop.notifications.failed_to_remove'))
                                    ->body(__('arma-reforger-workshop::arma-reforger-workshop.notifications.config_update_failed'))
                                    ->danger()
                                    ->send();
                            }
                        } catch (Exception $exception) {
                            report($exception);

                            Notification::make()
                                ->title(__('arma-reforger-workshop::arma-reforger-workshop.notifications.failed_to_remove'))
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ]);
    }

    /**
     * Enrich mods with workshop details, using concurrent requests for uncached mods.
     *
     * @param  array<int, array{modId: string, name: string, version: string}>  $mods
     * @return array<int, array<string, mixed>>
     */
    protected function enrichModsWithDetails(array $mods): array
    {
        $cachePrefix = 'arma_reforger_mod:';
        $cacheTtl = now()->addHours(6);

        // Separate cached and uncached mods
        $cachedDetails = [];
        $uncachedMods = [];

        foreach ($mods as $index => $mod) {
            $cacheKey = $cachePrefix . $mod['modId'];
            $cached = cache()->get($cacheKey);

            if ($cached !== null) {
                $cachedDetails[$index] = $cached;
            } else {
                $uncachedMods[$index] = $mod;
            }
        }

        // Fetch uncached mod details concurrently
        if (!empty($uncachedMods)) {
            $workshopUrl = ArmaReforgerWorkshopService::WORKSHOP_URL;

            $responses = Http::pool(function ($pool) use ($uncachedMods, $workshopUrl) {
                $requests = [];
                foreach ($uncachedMods as $index => $mod) {
                    $requests[$index] = $pool->as((string) $index)
                        ->timeout(10)
                        ->connectTimeout(5)
                        ->get($workshopUrl . '/' . $mod['modId']);
                }

                return $requests;
            });

            foreach ($uncachedMods as $index => $mod) {
                $response = $responses[(string) $index] ?? null;
                $details = $this->parseModDetailsFromResponse($response, $mod['modId']);

                // Cache the result
                cache()->put($cachePrefix . $mod['modId'], $details, $cacheTtl);
                $cachedDetails[$index] = $details;
            }
        }

        // Merge details with original mods
        return collect($mods)->map(function ($mod, $index) use ($cachedDetails) {
            $details = $cachedDetails[$index] ?? ['modId' => $mod['modId']];

            return array_merge($mod, $details);
        })->toArray();
    }

    /**
     * Parse mod details from HTTP response.
     *
     * @return array<string, mixed>
     */
    protected function parseModDetailsFromResponse($response, string $modId): array
    {
        try {
            if (!$response || !$response->successful()) {
                return ['modId' => $modId];
            }

            $html = $response->body();
            $details = ['modId' => $modId];

            // Extract data from __NEXT_DATA__ JSON
            if (preg_match('/<script[^>]*id="__NEXT_DATA__"[^>]*>(.+?)<\/script>/s', $html, $jsonMatch)) {
                $jsonData = json_decode($jsonMatch[1], true);

                if ($jsonData && isset($jsonData['props']['pageProps']['asset'])) {
                    $asset = $jsonData['props']['pageProps']['asset'];

                    $details['name'] = $asset['name'] ?? null;
                    $details['version'] = $asset['currentVersionNumber'] ?? null;
                    $details['subscribers'] = $asset['subscriberCount'] ?? null;

                    if (isset($asset['averageRating'])) {
                        $details['rating'] = (int) round($asset['averageRating'] * 100);
                    }
                }

                if (isset($jsonData['props']['pageProps']['getAssetDownloadTotal']['total'])) {
                    $details['downloads'] = $jsonData['props']['pageProps']['getAssetDownloadTotal']['total'];
                }
            }

            return array_filter($details, fn ($v) => $v !== null);
        } catch (Exception $exception) {
            report($exception);

            return ['modId' => $modId];
        }
    }

    protected function getHeaderActions(): array
    {
        /** @var Server $server */
        $server = Filament::getTenant();

        return [
            Action::make('add_mod')
                ->label('Add Mod')
                ->icon('tabler-plus')
                ->form([
                    TextInput::make('modId')
                        ->label('Mod ID')
                        ->helperText('Enter the mod ID (GUID) from the Bohemia Workshop. Example: 5965550F24A0C152')
                        ->placeholder('5965550F24A0C152')
                        ->required()
                        ->maxLength(16)
                        ->minLength(16)
                        ->regex('/^[A-Fa-f0-9]{16}$/')
                        ->validationMessages([
                            'regex' => 'The mod ID must be a 16-character hexadecimal string.',
                        ]),
                    TextInput::make('name')
                        ->label('Mod Name')
                        ->helperText('A friendly name for the mod')
                        ->required(),
                    TextInput::make('version')
                        ->label('Version')
                        ->placeholder('Optional - leave empty for latest'),
                ])
                ->action(function (array $data, DaemonFileRepository $fileRepository) {
                    try {
                        /** @var Server $server */
                        $server = Filament::getTenant();

                        $success = ArmaReforgerWorkshop::addMod(
                            $server,
                            $fileRepository,
                            strtoupper($data['modId']),
                            $data['name'],
                            $data['version'] ?? ''
                        );

                        if ($success) {
                            Notification::make()
                                ->title(__('arma-reforger-workshop::arma-reforger-workshop.notifications.mod_added'))
                                ->body(__('arma-reforger-workshop::arma-reforger-workshop.notifications.mod_added_body', ['name' => $data['name']]))
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title(__('arma-reforger-workshop::arma-reforger-workshop.notifications.failed_to_add'))
                                ->body(__('arma-reforger-workshop::arma-reforger-workshop.notifications.config_update_failed'))
                                ->danger()
                                ->send();
                        }
                    } catch (Exception $exception) {
                        report($exception);

                        Notification::make()
                            ->title(__('arma-reforger-workshop::arma-reforger-workshop.notifications.failed_to_add'))
                            ->body($exception->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Action::make('browse_workshop')
                ->label('Browse Workshop')
                ->icon('tabler-world-search')
                ->url(fn () => BrowseWorkshopPage::getUrl()),
            Action::make('open_workshop_external')
                ->label('Open in Browser')
                ->icon('tabler-external-link')
                ->url(ArmaReforgerWorkshopService::WORKSHOP_URL, true),
            Action::make('open_config')
                ->label('Edit Config')
                ->icon('tabler-file-settings')
                ->url(fn () => ListFiles::getUrl(['path' => rtrim(dirname(ArmaReforgerWorkshop::getConfigPath($server)), '.') ?: '/']), true),
        ];
    }

    public function content(Schema $schema): Schema
    {
        /** @var Server $server */
        $server = Filament::getTenant();

        return $schema
            ->components([
                Grid::make()
                    ->schema([
                        TextEntry::make('config_path')
                            ->label('Config Path')
                            ->state(fn () => ArmaReforgerWorkshop::getConfigPath($server))
                            ->badge(),
                        TextEntry::make('installed_mods')
                            ->label('Installed Mods')
                            ->state(function () use ($server) {
                                try {
                                    /** @var DaemonFileRepository $fileRepository */
                                    $fileRepository = app(DaemonFileRepository::class);

                                    return count(ArmaReforgerWorkshop::getInstalledMods($server, $fileRepository));
                                } catch (Exception $exception) {
                                    report($exception);

                                    return 'Unknown';
                                }
                            })
                            ->badge(),
                    ]),
                EmbeddedTable::make(),
            ]);
    }
}
