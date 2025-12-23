<?php

namespace Boy132\RustUMod\Filament\Server\Pages;

use App\Filament\Server\Resources\Files\Pages\ListFiles;
use App\Models\Server;
use App\Repositories\Daemon\DaemonFileRepository;
use App\Traits\Filament\BlockAccessInConflict;
use Boy132\RustUMod\Facades\RustUMod;
use Exception;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class RustUModPluginsPage extends Page implements HasTable
{
    use BlockAccessInConflict;
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-packages';

    protected static ?string $slug = 'umod';

    protected static ?int $navigationSort = 30;

    public static function canAccess(): bool
    {
        /** @var Server $server */
        $server = Filament::getTenant();

        return parent::canAccess() && RustUMod::isRustServer($server);
    }

    public static function getNavigationLabel(): string
    {
        return 'Plugins';
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
                $response = RustUMod::getUModPlugins($page, $search ?? '');

                return new LengthAwarePaginator($response['data'], $response['total'], 10, $page);
            })
            ->paginated([10])
            ->columns([
                ImageColumn::make('icon_url')
                    ->label(''),
                TextColumn::make('title')
                    ->searchable()
                    ->description(fn (array $record) => (strlen($record['description']) > 120) ? substr($record['description'], 0, 120).'...' : $record['description']),
                TextColumn::make('author')
                    ->url(fn ($state) => "https://umod.org/user/$state", true)
                    ->toggleable(),
                TextColumn::make('downloads_shortened')
                    ->label('Downloads')
                    ->icon('tabler-download')
                    ->toggleable(),
                TextColumn::make('latest_release_at')
                    ->icon('tabler-calendar')
                    ->formatStateUsing(fn ($state) => Carbon::parse($state, 'UTC')->diffForHumans())
                    ->tooltip(fn ($state) => Carbon::parse($state, 'UTC')->timezone(user()->timezone ?? 'UTC')->format($table->getDefaultDateTimeDisplayFormat()))
                    ->toggleable(),
            ])
            ->recordUrl(fn (array $record) => $record['url'], true)
            ->recordActions([
                Action::make('download')
                    ->action(function (array $record, DaemonFileRepository $fileRepository) {
                        try {
                            /** @var Server $server */
                            $server = Filament::getTenant();

                            $fileRepository->setServer($server)->pull($record['download_url'], 'oxide/plugins');

                            Notification::make()
                                ->title('Download started')
                                ->body($record['latest_release_version'])
                                ->success()
                                ->send();
                        } catch (Exception $exception) {
                            report($exception);

                            Notification::make()
                                ->title('Download could not be started')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('open_folder')
                ->label(fn () => 'Open plugins folder')
                ->url(fn () => ListFiles::getUrl(['path' => 'oxide/plugins']), true),
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
                        TextEntry::make('framework')
                            ->label(fn () => 'Modding Framework')
                            ->state(fn () => RustUMod::getRustModdingFramework($server))
                            ->badge(),
                        TextEntry::make('installed')
                            ->label(fn () => 'Installed plugins')
                            ->state(function (DaemonFileRepository $fileRepository) use ($server) {
                                try {
                                    $files = $fileRepository->setServer($server)->getDirectory('oxide/plugins');

                                    if (isset($files['error'])) {
                                        throw new Exception($files['error']);
                                    }

                                    return collect($files)
                                        ->filter(fn ($file) => $file['mime'] === 'text/plain' && str($file['name'])->lower()->endsWith('.cs'))
                                        ->count();
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
