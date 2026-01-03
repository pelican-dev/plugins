<?php

namespace spolny\ArmaReforgerWorkshop\Filament\Server\Pages;

use App\Models\Server;
use App\Repositories\Daemon\DaemonFileRepository;
use App\Traits\Filament\BlockAccessInConflict;
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
use spolny\ArmaReforgerWorkshop\Facades\ArmaReforgerWorkshop;

class BrowseWorkshopPage extends Page implements HasTable
{
    use BlockAccessInConflict;
    use InteractsWithTable;

    /** @var array<string>|null */
    protected ?array $installedModIds = null;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-world-search';

    protected static ?string $slug = 'workshop/browse';

    protected static ?int $navigationSort = 31;

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
        return trans('arma-reforger-workshop::arma-reforger-workshop.navigation.browse_workshop');
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
        return trans('arma-reforger-workshop::arma-reforger-workshop.titles.browse_workshop');
    }

    /** @return array<string> */
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
                    ->label(trans('arma-reforger-workshop::arma-reforger-workshop.labels.mod'))
                    ->weight('bold')
                    ->searchable()
                    ->description(fn (array $record) => \Illuminate\Support\Str::limit($record['summary'] ?? '', 80)),
                TextColumn::make('author')
                    ->label(trans('arma-reforger-workshop::arma-reforger-workshop.labels.author'))
                    ->icon('tabler-user')
                    ->toggleable(),
                TextColumn::make('version')
                    ->label(trans('arma-reforger-workshop::arma-reforger-workshop.labels.version'))
                    ->badge()
                    ->color('gray')
                    ->toggleable(),
                TextColumn::make('subscribers')
                    ->label(trans('arma-reforger-workshop::arma-reforger-workshop.labels.subscribers'))
                    ->icon('tabler-users')
                    ->numeric()
                    ->sortable(false)
                    ->toggleable(),
                TextColumn::make('rating')
                    ->label(trans('arma-reforger-workshop::arma-reforger-workshop.labels.rating'))
                    ->formatStateUsing(fn ($state) => $state ? "{$state}%" : '-')
                    ->toggleable(),
                TextColumn::make('type')
                    ->label(trans('arma-reforger-workshop::arma-reforger-workshop.labels.type'))
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
                    ->label(trans('arma-reforger-workshop::arma-reforger-workshop.actions.add_to_server'))
                    ->icon('tabler-plus')
                    ->color('success')
                    ->visible(fn (array $record) => !in_array(strtoupper($record['modId']), $this->getInstalledModIds(), true))
                    ->requiresConfirmation()
                    ->modalHeading(fn (array $record) => trans('arma-reforger-workshop::arma-reforger-workshop.modals.add_mod_heading', ['name' => $record['name']]))
                    ->modalDescription(fn (array $record) => trans('arma-reforger-workshop::arma-reforger-workshop.modals.add_mod_description', ['name' => $record['name'], 'author' => $record['author']]))
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
                                $this->installedModIds = null;

                                Notification::make()
                                    ->title(trans('arma-reforger-workshop::arma-reforger-workshop.notifications.mod_added'))
                                    ->body(trans('arma-reforger-workshop::arma-reforger-workshop.notifications.mod_added_body', ['name' => $record['name']]))
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
                Action::make('installed')
                    ->label(trans('arma-reforger-workshop::arma-reforger-workshop.actions.installed'))
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
                ->label(trans('arma-reforger-workshop::arma-reforger-workshop.actions.view_installed_mods'))
                ->icon('tabler-list')
                ->url(fn () => ArmaReforgerWorkshopPage::getUrl()),
            Action::make('open_workshop')
                ->label(trans('arma-reforger-workshop::arma-reforger-workshop.actions.open_in_browser'))
                ->icon('tabler-external-link')
                ->url('https://reforger.armaplatform.com/workshop', true),
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(trans('arma-reforger-workshop::arma-reforger-workshop.sections.browse_mods'))
                    ->description(trans('arma-reforger-workshop::arma-reforger-workshop.sections.browse_mods_description'))
                    ->schema([
                        EmbeddedTable::make(),
                    ]),
            ]);
    }
}
