<?php

namespace Boy132\MinecraftModrinth\Filament\Server\Pages;

use App\Filament\Server\Resources\Files\Pages\ListFiles;
use App\Models\Server;
use App\Repositories\Daemon\DaemonFileRepository;
use App\Traits\Filament\BlockAccessInConflict;
use Boy132\MinecraftModrinth\Enums\MinecraftLoader;
use Boy132\MinecraftModrinth\Enums\ModrinthProjectType;
use Boy132\MinecraftModrinth\Facades\MinecraftModrinth;
use Exception;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

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

        return parent::canAccess() && ModrinthProjectType::fromServer($server);
    }

    public static function getNavigationLabel(): string
    {
        /** @var Server $server */
        $server = Filament::getTenant();

        return ModrinthProjectType::fromServer($server)->getLabel();
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
                    ->description(fn (array $record) => (strlen($record['description']) > 120) ? substr($record['description'], 0, 120).'...' : $record['description']),
                TextColumn::make('author')
                    ->url(fn ($state) => "https://modrinth.com/user/$state", true)
                    ->toggleable(),
                TextColumn::make('downloads')
                    ->icon('tabler-download')
                    ->numeric()
                    ->toggleable(),
                TextColumn::make('date_modified')
                    ->icon('tabler-calendar')
                    ->formatStateUsing(fn ($state) => Carbon::parse($state, 'UTC')->diffForHumans())
                    ->tooltip(fn ($state) => Carbon::parse($state, 'UTC')->timezone(user()->timezone ?? 'UTC')->format($table->getDefaultDateTimeDisplayFormat()))
                    ->toggleable(),
            ])
            ->recordUrl(fn (array $record) => "https://modrinth.com/{$record['project_type']}/{$record['slug']}", true)
            ->recordActions([
                Action::make('download')
                    ->schema(function (array $record) {
                        $schema = [];

                        /** @var Server $server */
                        $server = Filament::getTenant();

                        $versions = array_slice(MinecraftModrinth::getModrinthVersions($record['project_id'], $server), 0, 10);
                        foreach ($versions as $versionData) {
                            $files = $versionData['files'] ?? [];
                            $primaryFile = null;

                            foreach ($files as $fileData) {
                                if ($fileData['primary']) {
                                    $primaryFile = $fileData;
                                    break;
                                }
                            }

                            $schema[] = Section::make($versionData['name'])
                                ->description($versionData['version_number'] . ($primaryFile ? ' (' . convert_bytes_to_readable($primaryFile['size']) . ')' : ' (' . trans('minecraft-modrinth::strings.version.no_file_found') . ')'))
                                ->collapsed(!$versionData['featured'])
                                ->collapsible()
                                ->icon($versionData['version_type'] === 'alpha' ? 'tabler-circle-letter-a' : ($versionData['version_type'] === 'beta' ? 'tabler-circle-letter-b' : 'tabler-circle-letter-r'))
                                ->iconColor($versionData['version_type'] === 'alpha' ? 'danger' : ($versionData['version_type'] === 'beta' ? 'warning' : 'success'))
                                ->columns(3)
                                ->schema([
                                    TextEntry::make('type')
                                        ->badge()
                                        ->color($versionData['version_type'] === 'alpha' ? 'danger' : ($versionData['version_type'] === 'beta' ? 'warning' : 'success'))
                                        ->state($versionData['version_type']),
                                    TextEntry::make('downloads')
                                        ->badge()
                                        ->state($versionData['downloads']),
                                    TextEntry::make('published')
                                        ->badge()
                                        ->state(Carbon::parse($versionData['date_published'], 'UTC')->diffForHumans())
                                        ->tooltip(Carbon::parse($versionData['date_published'], 'UTC')->timezone(user()->timezone ?? 'UTC')->format('M j, Y H:i:s')),
                                    TextEntry::make('changelog')
                                        ->columnSpanFull()
                                        ->markdown()
                                        ->state($versionData['changelog']),
                                ])
                                ->headerActions([
                                    Action::make('download')
                                        ->visible(!is_null($primaryFile))
                                        ->action(function (DaemonFileRepository $fileRepository) use ($server, $versionData, $primaryFile) {
                                            try {
                                                $fileRepository->setServer($server)->pull($primaryFile['url'], ModrinthProjectType::fromServer($server)->getFolder());

                                                Notification::make()
                                                    ->title(trans('minecraft-modrinth::strings.notifications.download_started'))
                                                    ->body($versionData['name'])
                                                    ->success()
                                                    ->send();
                                            } catch (Exception $exception) {
                                                report($exception);

                                                Notification::make()
                                                    ->title(trans('minecraft-modrinth::strings.notifications.download_failed'))
                                                    ->body($exception->getMessage())
                                                    ->danger()
                                                    ->send();
                                            }
                                        }),
                                ]);
                        }

                        return $schema;
                    }),
            ]);
    }

    protected function getHeaderActions(): array
    {
        /** @var Server $server */
        $server = Filament::getTenant();

        $folder = ModrinthProjectType::fromServer($server)->getFolder();

        return [
            Action::make('open_folder')
                ->label(fn () => trans('minecraft-modrinth::strings.page.open_folder', ['folder' => $folder]))
                ->url(fn () => ListFiles::getUrl(['path' => $folder]), true),
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
                            ->state(fn () => MinecraftModrinth::getMinecraftVersion($server) ?? trans('minecraft-modrinth::strings.page.unknown'))
                            ->badge(),
                        TextEntry::make('Loader')
                            ->state(fn () => MinecraftLoader::fromServer($server)?->getLabel() ?? trans('minecraft-modrinth::strings.page.unknown'))
                            ->badge(),
                        TextEntry::make('installed')
                            ->label(fn () => trans('minecraft-modrinth::strings.page.installed', ['type' => ModrinthProjectType::fromServer($server)->getLabel()]))
                            ->state(function (DaemonFileRepository $fileRepository) use ($server) {
                                try {
                                    $files = $fileRepository->setServer($server)->getDirectory(ModrinthProjectType::fromServer($server)->getFolder());

                                    if (isset($files['error'])) {
                                        throw new Exception($files['error']);
                                    }

                                    return collect($files)
                                        ->filter(fn ($file) => $file['mime'] === 'application/jar' || str($file['name'])->lower()->endsWith('.jar'))
                                        ->count();
                                } catch (Exception $exception) {
                                    report($exception);

                                    return trans('minecraft-modrinth::strings.page.unknown');
                                }
                            })
                            ->badge(),
                    ]),
                EmbeddedTable::make(),
            ]);
    }
}
