<?php

namespace Boy132\MinecraftModrinth\Filament\Server\Pages;

use App\Filament\Server\Resources\Files\Pages\ListFiles;
use App\Models\Server;
use App\Repositories\Daemon\DaemonFileRepository;
use App\Traits\Filament\BlockAccessInConflict;
use Boy132\MinecraftModrinth\Facades\MinecraftModrinth;
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

class MinecraftModrinthProjectPage extends Page implements HasTable
{
    use BlockAccessInConflict;
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-packages';

    protected static ?string $slug = 'modrinth';

    protected static ?int $navigationSort = 30;

    public static function canAccess(): bool
    {
        /** @var Server $server */
        $server = Filament::getTenant();

        return parent::canAccess() && MinecraftModrinth::getModrinthProjectType($server);
    }

    public static function getNavigationLabel(): string
    {
        /** @var Server $server */
        $server = Filament::getTenant();

        return MinecraftModrinth::getModrinthProjectType($server)->getLabel();
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

                $response = MinecraftModrinth::getModrinthProjects($server, $page, $search);

                return new LengthAwarePaginator($response['hits'], $response['total_hits'], 20, $page);
            })
            ->paginated([20])
            ->columns([
                ImageColumn::make('icon_url')
                    ->label(''),
                TextColumn::make('title')
                    ->searchable()
                    ->description(function (array $record) {
                        /** @var Server $server */
                        $server = Filament::getTenant();

                        $versions = MinecraftModrinth::getModrinthVersions($record['project_id'], $server);
                        if (count($versions) > 0) {
                            $version = $versions[0];
                            $files = $version['files'] ?? [];

                            foreach ($files as $data) {
                                if ($data['primary']) {
                                    $size = convert_bytes_to_readable($data['size']);

                                    return "{$version['version_number']} ({$size})";
                                }
                            }
                        }

                        return null;
                    }, 'above')
                    ->description(fn (array $record) => (strlen($record['description']) > 120) ? substr($record['description'], 0, 129).'...' : $record['description'], 'below'),
                TextColumn::make('author')
                    ->url(fn ($state) => "https://modrinth.com/user/$state", true),
                TextColumn::make('downloads')
                    ->icon('tabler-download')
                    ->numeric(),
            ])
            ->recordUrl(fn (array $record) => "https://modrinth.com/{$record['project_type']}/{$record['slug']}", true)
            ->recordActions([
                Action::make('download')
                    ->action(function (array $record, DaemonFileRepository $fileRepository) {
                        /** @var Server $server */
                        $server = Filament::getTenant();

                        $versions = MinecraftModrinth::getModrinthVersions($record['project_id'], $server);
                        if (count($versions) > 0) {
                            $version = $versions[0];
                            $files = $version['files'] ?? [];

                            foreach ($files as $data) {
                                if ($data['primary']) {
                                    $url = $data['url'];

                                    try {
                                        $fileRepository->setServer($server)->pull($url, MinecraftModrinth::getModrinthProjectType($server)->getFolder());

                                        Notification::make()
                                            ->title('Download started')
                                            ->body($version['name'])
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

                                    return;
                                }
                            }
                        }

                        Notification::make()
                            ->title('Download could not be started')
                            ->body('No (compatible) version found.')
                            ->danger()
                            ->send();
                    }),
            ]);
    }

    protected function getHeaderActions(): array
    {
        /** @var Server $server */
        $server = Filament::getTenant();

        return [
            Action::make('open_folder')
                ->label(fn () => 'Open ' . MinecraftModrinth::getModrinthProjectType($server)->getFolder() . ' folder')
                ->url(fn () => ListFiles::getUrl(['path' => MinecraftModrinth::getModrinthProjectType($server)->getFolder()]), true),
        ];
    }

    public function content(Schema $schema): Schema
    {
        /** @var Server $server */
        $server = Filament::getTenant();

        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        TextEntry::make('Minecraft Version')
                            ->state(fn () => MinecraftModrinth::getMinecraftVersion($server) ?? 'Unknown')
                            ->badge(),
                        TextEntry::make('Loader')
                            ->state(fn () => str(MinecraftModrinth::getMinecraftLoader($server) ?? 'Unknown')->title())
                            ->badge(),
                        TextEntry::make('installed')
                            ->label(fn () => 'Installed ' . MinecraftModrinth::getModrinthProjectType($server)->getLabel())
                            ->state(function (DaemonFileRepository $fileRepository) use ($server) {
                                try {
                                    return collect($fileRepository->setServer($server)->getDirectory(MinecraftModrinth::getModrinthProjectType($server)->getFolder()))
                                        ->filter(fn ($file) => $file['mimetype'] === 'application/jar' || str($file['name'])->endsWith('.jar'))
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
