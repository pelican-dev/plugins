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
        /** @var Server|null $server */
        $server = Filament::getTenant();

        if (!$server) {
            return false;
        }

        return parent::canAccess() && ArmaReforgerWorkshop::isArmaReforgerServer($server);
    }

    public static function getNavigationLabel(): string
    {
        return trans('arma-reforger-workshop::arma-reforger-workshop.navigation.workshop_mods');
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
                    ->label(trans('arma-reforger-workshop::arma-reforger-workshop.labels.mod_name'))
                    ->searchable()
                    ->description(fn (array $record) => $record['modId']),
                TextColumn::make('version')
                    ->label(trans('arma-reforger-workshop::arma-reforger-workshop.labels.version'))
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('subscribers')
                    ->label(trans('arma-reforger-workshop::arma-reforger-workshop.labels.subscribers'))
                    ->icon('tabler-users')
                    ->numeric()
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('downloads')
                    ->label(trans('arma-reforger-workshop::arma-reforger-workshop.labels.downloads'))
                    ->icon('tabler-download')
                    ->numeric()
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('rating')
                    ->label(trans('arma-reforger-workshop::arma-reforger-workshop.labels.rating'))
                    ->formatStateUsing(fn ($state) => $state ? "{$state}%" : '-')
                    ->toggleable(),
            ])
            ->recordUrl(fn (array $record) => ArmaReforgerWorkshop::getModWorkshopUrl($record['modId']), true)
            ->recordActions([
                Action::make('remove')
                    ->label(trans('arma-reforger-workshop::arma-reforger-workshop.actions.remove'))
                    ->icon('tabler-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading(trans('arma-reforger-workshop::arma-reforger-workshop.modals.remove_mod_heading'))
                    ->modalDescription(fn (array $record) => trans('arma-reforger-workshop::arma-reforger-workshop.modals.remove_mod_description', ['name' => $record['name']]))
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
                                    ->title(trans('arma-reforger-workshop::arma-reforger-workshop.notifications.mod_removed'))
                                    ->body(trans('arma-reforger-workshop::arma-reforger-workshop.notifications.mod_removed_body', ['name' => $record['name']]))
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title(trans('arma-reforger-workshop::arma-reforger-workshop.notifications.failed_to_remove'))
                                    ->body(trans('arma-reforger-workshop::arma-reforger-workshop.notifications.config_update_failed'))
                                    ->danger()
                                    ->send();
                            }
                        } catch (Exception $exception) {
                            report($exception);

                            Notification::make()
                                ->title(trans('arma-reforger-workshop::arma-reforger-workshop.notifications.failed_to_remove'))
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
                $details = (!$response || !$response->successful()) ? ['modId' => $mod['modId']] : ArmaReforgerWorkshop::parseNextDataFromHtml($response->body(), $mod['modId']);

                // Cache the result: use long TTL for successful responses, short TTL for failures
                $isSuccessfulResponse = isset($details['name']) && count($details) > 1;
                if ($isSuccessfulResponse) {
                    cache()->put($cachePrefix . $mod['modId'], $details, $cacheTtl);
                } else {
                    // Mark as failed and cache with short TTL (2 minutes) to allow retries
                    $details['failed'] = true;
                    cache()->put($cachePrefix . $mod['modId'], $details, now()->addMinutes(2));
                }
                $cachedDetails[$index] = $details;
            }
        }

        // Merge details with original mods
        return collect($mods)->map(function ($mod, $index) use ($cachedDetails) {
            $details = $cachedDetails[$index] ?? ['modId' => $mod['modId']];

            return array_merge($mod, $details);
        })->toArray();
    }

    protected function getHeaderActions(): array
    {
        /** @var Server $server */
        $server = Filament::getTenant();

        return [
            Action::make('add_mod')
                ->label(trans('arma-reforger-workshop::arma-reforger-workshop.actions.add_mod'))
                ->icon('tabler-plus')
                ->form([
                    TextInput::make('modId')
                        ->label(trans('arma-reforger-workshop::arma-reforger-workshop.labels.mod_id'))
                        ->helperText(trans('arma-reforger-workshop::arma-reforger-workshop.form.mod_id_helper'))
                        ->placeholder(trans('arma-reforger-workshop::arma-reforger-workshop.form.mod_id_placeholder'))
                        ->required()
                        ->maxLength(16)
                        ->minLength(16)
                        ->regex('/^[A-Fa-f0-9]{16}$/')
                        ->validationMessages([
                            'regex' => trans('arma-reforger-workshop::arma-reforger-workshop.form.mod_id_validation_regex'),
                        ]),
                    TextInput::make('name')
                        ->label(trans('arma-reforger-workshop::arma-reforger-workshop.labels.mod_name'))
                        ->helperText(trans('arma-reforger-workshop::arma-reforger-workshop.form.mod_name_helper'))
                        ->required(),
                    TextInput::make('version')
                        ->label(trans('arma-reforger-workshop::arma-reforger-workshop.labels.version'))
                        ->placeholder(trans('arma-reforger-workshop::arma-reforger-workshop.form.version_placeholder')),
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
                                ->title(trans('arma-reforger-workshop::arma-reforger-workshop.notifications.mod_added'))
                                ->body(trans('arma-reforger-workshop::arma-reforger-workshop.notifications.mod_added_body', ['name' => $data['name']]))
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title(trans('arma-reforger-workshop::arma-reforger-workshop.notifications.failed_to_add'))
                                ->body(trans('arma-reforger-workshop::arma-reforger-workshop.notifications.config_update_failed'))
                                ->danger()
                                ->send();
                        }
                    } catch (Exception $exception) {
                        report($exception);

                        Notification::make()
                            ->title(trans('arma-reforger-workshop::arma-reforger-workshop.notifications.failed_to_add'))
                            ->body($exception->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Action::make('browse_workshop')
                ->label(trans('arma-reforger-workshop::arma-reforger-workshop.actions.browse_workshop'))
                ->icon('tabler-world-search')
                ->url(fn () => BrowseWorkshopPage::getUrl()),
            Action::make('open_workshop_external')
                ->label(trans('arma-reforger-workshop::arma-reforger-workshop.actions.open_in_browser'))
                ->icon('tabler-external-link')
                ->url(ArmaReforgerWorkshopService::WORKSHOP_URL, true),
            Action::make('open_config')
                ->label(trans('arma-reforger-workshop::arma-reforger-workshop.actions.edit_config'))
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
                            ->label(trans('arma-reforger-workshop::arma-reforger-workshop.labels.config_path'))
                            ->state(fn () => ArmaReforgerWorkshop::getConfigPath($server))
                            ->badge(),
                        TextEntry::make('installed_mods')
                            ->label(trans('arma-reforger-workshop::arma-reforger-workshop.labels.installed_mods'))
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
