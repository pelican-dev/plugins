<?php

namespace Boy132\PlayerCounter\Filament\Server\Pages;

use App\Models\Server;
use App\Repositories\Daemon\DaemonFileRepository;
use App\Traits\Filament\BlockAccessInConflict;
use Boy132\PlayerCounter\Filament\Server\Widgets\ServerPlayerWidget;
use Boy132\PlayerCounter\Models\GameQuery;
use Carbon\CarbonInterval;
use Exception;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Schema;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Pagination\LengthAwarePaginator;

class PlayersPage extends Page implements HasTable
{
    use BlockAccessInConflict;
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-users-group';

    protected static ?string $slug = 'players';

    protected static ?int $navigationSort = 30;

    public static function canAccess(): bool
    {
        /** @var Server $server */
        $server = Filament::getTenant();

        return parent::canAccess() && $server->allocation && $server->egg->gameQuery()->exists(); // @phpstan-ignore method.notFound
    }

    public static function getNavigationLabel(): string
    {
        return trans('player-counter::query.players');
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
        /** @var Server $server */
        $server = Filament::getTenant();

        /** @var ?GameQuery $gameQuery */
        $gameQuery = $server->egg->gameQuery; // @phpstan-ignore property.notFound

        $isMinecraft = $gameQuery?->query_type->isMinecraft();

        $whitelist = [];
        $ops = [];

        if ($isMinecraft) {
            $fileRepository = (new DaemonFileRepository())->setServer($server);

            try {
                $whitelist = json_decode($fileRepository->getContent('whitelist.json'), true, 512, JSON_THROW_ON_ERROR);
                $whitelist = array_unique(array_map(fn ($data) => $data['name'], $whitelist));
            } catch (Exception $exception) {
                report($exception);
            }

            try {
                $ops = json_decode($fileRepository->getContent('ops.json'), true, 512, JSON_THROW_ON_ERROR);
                $ops = array_unique(array_map(fn ($data) => $data['name'], $ops));
            } catch (Exception $exception) {
                report($exception);
            }
        }

        return $table
            ->records(function (?string $search, int $page, int $recordsPerPage) {
                /** @var Server $server */
                $server = Filament::getTenant();

                $players = [];

                /** @var ?GameQuery $gameQuery */
                $gameQuery = $server->egg->gameQuery; // @phpstan-ignore property.notFound

                if ($gameQuery) {
                    $data = $gameQuery->runQuery($server->allocation);
                    $players = $data['players'];
                }

                if ($search) {
                    $players = array_filter($players, fn ($player) => str($player['player'])->contains($search, true));
                }

                return new LengthAwarePaginator(array_slice($players, ($page - 1) * $recordsPerPage, $recordsPerPage), count($players), $recordsPerPage, $page);
            })
            ->paginated([30, 60])
            ->contentGrid([
                'default' => 1,
                'lg' => 2,
                'xl' => 3,
            ])
            ->columns([
                Split::make([
                    ImageColumn::make('avatar')
                        ->visible(fn () => $isMinecraft)
                        ->state(fn (array $record) => 'https://cravatar.eu/helmhead/' . $record['player'] . '/256.png')
                        ->grow(false),
                    TextColumn::make('player')
                        ->label('Name')
                        ->searchable(),
                    TextColumn::make('is_whitelisted')
                        ->visible(fn () => $isMinecraft)
                        ->badge()
                        ->grow(false)
                        ->state(fn (array $record) => in_array($record['player'], $whitelist) ? trans('player-counter::query.whitelisted') : null),
                    TextColumn::make('is_op')
                        ->visible(fn () => $isMinecraft)
                        ->badge()
                        ->grow(false)
                        ->state(fn (array $record) => in_array($record['player'], $ops) ? trans('player-counter::query.op') : null),
                    TextColumn::make('time')
                        ->hidden(fn () => $isMinecraft)
                        ->badge()
                        ->grow(false)
                        ->formatStateUsing(fn ($state) => $state ? CarbonInterval::seconds($state)->cascade()->forHumans() : null),
                ]),
            ])
            ->recordActions([
                Action::make('kick')
                    ->visible(fn () => $isMinecraft)
                    ->icon('tabler-door-exit')
                    ->color('danger')
                    ->action(function (array $record) {
                        /** @var Server $server */
                        $server = Filament::getTenant();

                        try {
                            $server->send('kick ' . $record['player']);

                            Notification::make()
                                ->title(trans('player-counter::query.notifications.player_kicked'))
                                ->body($record['player'])
                                ->success()
                                ->send();

                            $this->refreshPage();
                        } catch (Exception $exception) {
                            report($exception);

                            Notification::make()
                                ->title(trans('player-counter::query.notifications.player_kick_failed'))
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('whitelist')
                    ->visible(fn () => $isMinecraft)
                    ->label(fn (array $record) => in_array($record['player'], $whitelist) ? trans('player-counter::query.remove_from_whitelist') : trans('player-counter::query.add_to_whitelist'))
                    ->icon(fn (array $record) => in_array($record['player'], $whitelist) ? 'tabler-playlist-x' : 'tabler-playlist-add')
                    ->color(fn (array $record) => in_array($record['player'], $whitelist) ? 'danger' : 'success')
                    ->action(function (array $record) use ($whitelist) {
                        /** @var Server $server */
                        $server = Filament::getTenant();

                        try {
                            $action = in_array($record['player'], $whitelist) ? 'remove' : 'add';

                            $server->send('whitelist ' . $action . ' ' . $record['player']);

                            Notification::make()
                                ->title(trans('player-counter::query.notifications.player_whitelist_' . $action))
                                ->body($record['player'])
                                ->success()
                                ->send();

                            $this->refreshPage();
                        } catch (Exception $exception) {
                            report($exception);

                            Notification::make()
                                ->title(trans('player-counter::query.notifications.player_whitelist_failed'))
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('op')
                    ->visible(fn () => $isMinecraft)
                    ->label(fn (array $record) => in_array($record['player'], $ops) ? trans('player-counter::query.remove_from_ops') : trans('player-counter::query.add_to_ops'))
                    ->icon(fn (array $record) => in_array($record['player'], $ops) ? 'tabler-shield-minus' : 'tabler-shield-plus')
                    ->color(fn (array $record) => in_array($record['player'], $ops) ? 'warning' : 'success')
                    ->action(function (array $record) use ($ops) {
                        /** @var Server $server */
                        $server = Filament::getTenant();

                        try {
                            $action = in_array($record['player'], $ops) ? 'deop' : 'op';

                            $server->send($action  . ' ' . $record['player']);

                            Notification::make()
                                ->title(trans('player-counter::query.notifications.player_' . $action))
                                ->body($record['player'])
                                ->success()
                                ->send();

                            $this->refreshPage();
                        } catch (Exception $exception) {
                            report($exception);

                            Notification::make()
                                ->title(trans('player-counter::query.notifications.player_op_failed'))
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->emptyStateHeading(function () {
                /** @var Server $server */
                $server = Filament::getTenant();

                if ($server->retrieveStatus()->isOffline()) {
                    return trans('player-counter::query.table.server_offline');
                }

                return trans('player-counter::query.table.no_players');
            })
            ->emptyStateDescription(function () {
                /** @var Server $server */
                $server = Filament::getTenant();

                if ($server->retrieveStatus()->isOffline()) {
                    return null;
                }

                return trans('player-counter::query.table.no_players_description');
            });
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                EmbeddedTable::make(),
            ]);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ServerPlayerWidget::class,
        ];
    }

    private function refreshPage(): void
    {
        $url = self::getUrl();
        $this->redirect($url, FilamentView::hasSpaMode($url));
    }
}
