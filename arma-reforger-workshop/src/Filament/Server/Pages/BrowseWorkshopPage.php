<?php

namespace spolny\ArmaReforgerWorkshop\Filament\Server\Pages;

use App\Models\Server;
use App\Repositories\Daemon\DaemonFileRepository;
use App\Traits\Filament\BlockAccessInConflict;
use spolny\ArmaReforgerWorkshop\Facades\ArmaReforgerWorkshop;
use Exception;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Pagination\LengthAwarePaginator;

class BrowseWorkshopPage extends Page implements HasTable
{
    use BlockAccessInConflict;
    use InteractsWithTable;

    protected ?array $installedModIds = null;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-world-search';

    protected static ?string $slug = 'workshop/browse';

    protected static ?int $navigationSort = 31;

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
        return 'Browse Workshop';
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
        return 'Browse Arma Reforger Workshop';
    }

    protected function getInstalledModIds(): array
    {
        if ($this->installedModIds === null) {
            /** @var Server $server */
            $server = Filament::getTenant();
            /** @var DaemonFileRepository $fileRepository */
            $fileRepository = app(DaemonFileRepository::class);

            $installedMods = ArmaReforgerWorkshop::getInstalledMods($server, $fileRepository);
            $this->installedModIds = array_map(
                fn (array $mod) => strtoupper($mod['modId']),
                $installedMods
            );
        }

        return $this->installedModIds;
    }

    /**
     * @throws Exception
     */
    public function table(Table $table): Table
    {
        return $table
            ->searchable()
            ->records(function (?string $search, int $page) {
                $result = ArmaReforgerWorkshop::browseWorkshop($search ?? '', $page);

                return new LengthAwarePaginator(
                    $result['mods'],
                    $result['total'],
                    $result['perPage'],
                    $result['page']
                );
            })
            ->deferLoading()
            ->paginated([24])
            ->columns([
                ImageColumn::make('thumbnail')
                    ->label('')
                    ->size(60)
                    ->extraImgAttributes(['class' => 'rounded'])
                    ->defaultImageUrl(fn () => 'https://reforger.armaplatform.com/favicon.ico'),
                TextColumn::make('name')
                    ->label('Mod')
                    ->weight('bold')
                    ->searchable()
                    ->description(fn (array $record) => \Illuminate\Support\Str::limit($record['summary'] ?? '', 80)),
                TextColumn::make('author')
                    ->label('Author')
                    ->icon('tabler-user')
                    ->toggleable(),
                TextColumn::make('version')
                    ->label('Version')
                    ->badge()
                    ->color('gray')
                    ->toggleable(),
                TextColumn::make('subscribers')
                    ->label('Subscribers')
                    ->icon('tabler-users')
                    ->numeric()
                    ->sortable(false)
                    ->toggleable(),
                TextColumn::make('rating')
                    ->label('Rating')
                    ->formatStateUsing(fn ($state) => $state ? "{$state}%" : '-')
                    ->toggleable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'scenario' => 'info',
                        'addon' => 'success',
                        default => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordUrl(fn (array $record) => ArmaReforgerWorkshop::getModWorkshopUrl($record['modId']), true)
            ->recordActions([
                Action::make('install')
                    ->label('Add to Server')
                    ->icon('tabler-plus')
                    ->color('success')
                    ->visible(fn (array $record) => !in_array(strtoupper($record['modId']), $this->getInstalledModIds(), true))
                    ->requiresConfirmation()
                    ->modalHeading(fn (array $record) => "Add \"{$record['name']}\"")
                    ->modalDescription(fn (array $record) => "This will add \"{$record['name']}\" by {$record['author']} to your server's mod list.")
                    ->action(function (array $record, DaemonFileRepository $fileRepository) {
                        try {
                            /** @var Server $server */
                            $server = Filament::getTenant();

                            $success = ArmaReforgerWorkshop::addMod(
                                $server,
                                $fileRepository,
                                strtoupper($record['modId']),
                                $record['name'],
                                '' // Don't specify version to get latest
                            );

                            if ($success) {
                                Notification::make()
                                    ->title('Mod added')
                                    ->body("'{$record['name']}' has been added to your server configuration.")
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Failed to add mod')
                                    ->body('Could not update the server configuration.')
                                    ->danger()
                                    ->send();
                            }
                        } catch (Exception $exception) {
                            report($exception);

                            Notification::make()
                                ->title('Failed to add mod')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('installed')
                    ->label('Installed')
                    ->icon('tabler-check')
                    ->color('gray')
                    ->disabled()
                    ->visible(fn (array $record) => in_array(strtoupper($record['modId']), $this->getInstalledModIds(), true)),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_installed')
                ->label('View Installed Mods')
                ->icon('tabler-list')
                ->url(fn () => ArmaReforgerWorkshopPage::getUrl()),
            Action::make('open_workshop')
                ->label('Open in Browser')
                ->icon('tabler-external-link')
                ->url('https://reforger.armaplatform.com/workshop', true),
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Browse Mods')
                    ->description('Search and browse mods from the Bohemia Arma Reforger Workshop. Click "Add to Server" to install a mod.')
                    ->schema([
                        EmbeddedTable::make(),
                    ]),
            ]);
    }
}
