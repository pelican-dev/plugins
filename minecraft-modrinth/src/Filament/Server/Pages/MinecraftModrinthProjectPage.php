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
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class MinecraftModrinthProjectPage extends Page implements HasTable
{
    use BlockAccessInConflict;
    use InteractsWithTable;

    /** @var array<int, array{project_id: string, project_slug: string, project_title: string, version_id: string, version_number: string, filename: string, installed_at: string}>|null */
    protected ?array $installedModsMetadata = null;

    /** @var array<string, array<int, mixed>> Cache for version data by project_id */
    protected array $versionsCache = [];

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

        $type = ModrinthProjectType::fromServer($server);

        return $type?->getLabel() ?? 'Modrinth';
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

    /** @return array<int, array{project_id: string, project_slug: string, project_title: string, version_id: string, version_number: string, filename: string, installed_at: string}> */
    protected function getInstalledModsMetadata(): array
    {
        if ($this->installedModsMetadata === null) {
            /** @var Server $server */
            $server = Filament::getTenant();
            /** @var DaemonFileRepository $fileRepository */
            $fileRepository = app(DaemonFileRepository::class);

            $this->installedModsMetadata = MinecraftModrinth::getInstalledModsMetadata($server, $fileRepository);
        }

        return $this->installedModsMetadata;
    }

    /** @return array{project_id: string, project_slug: string, project_title: string, version_id: string, version_number: string, filename: string, installed_at: string}|null */
    protected function getInstalledMod(string $projectId): ?array
    {
        $installedMods = $this->getInstalledModsMetadata();

        foreach ($installedMods as $mod) {
            if ($mod['project_id'] === $projectId) {
                return $mod;
            }
        }

        return null;
    }

    /** @return array<int, mixed> */
    protected function getCachedVersions(string $projectId): array
    {
        if (!isset($this->versionsCache[$projectId])) {
            /** @var Server $server */
            $server = Filament::getTenant();
            $this->versionsCache[$projectId] = MinecraftModrinth::getModrinthVersions($projectId, $server);
        }

        return $this->versionsCache[$projectId];
    }

    /**
     * @param  array<int, array{primary: bool, filename: string, url: string}>  $files
     * @return array{primary: bool, filename: string, url: string}|null
     */
    protected function getPrimaryFile(array $files): ?array
    {
        foreach ($files as $file) {
            if (!empty($file['primary'])) {
                return $file;
            }
        }

        return null;
    }

    /**
     * Validate and sanitize filename to prevent path traversal
     *
     * @throws Exception
     */
    protected function validateFilename(string $filename): string
    {
        if ($filename === '' || str_contains($filename, '/') || str_contains($filename, '\\')) {
            throw new Exception('Invalid filename: potential path traversal detected');
        }

        return basename($filename);
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
                Action::make('install')
                    ->iconButton()
                    ->icon('tabler-download')
                    ->color('success')
                    ->tooltip(trans('minecraft-modrinth::strings.actions.install'))
                    ->visible(function (array $record) {
                        $installedMod = $this->getInstalledMod($record['project_id']);

                        return is_null($installedMod);
                    })
                    ->action(function (array $record, DaemonFileRepository $fileRepository) {
                        try {
                            /** @var Server $server */
                            $server = Filament::getTenant();

                            $versions = MinecraftModrinth::getModrinthVersions($record['project_id'], $server);

                            if (empty($versions)) {
                                throw new Exception('No compatible versions found');
                            }

                            $latestVersion = $versions[0];

                            if (!isset($latestVersion['id'], $latestVersion['version_number'], $latestVersion['files'])) {
                                throw new Exception('Invalid version data structure');
                            }

                            $primaryFile = $this->getPrimaryFile($latestVersion['files']);

                            if (!$primaryFile) {
                                throw new Exception('No downloadable file found');
                            }

                            // Validate filename from Modrinth API to prevent path traversal
                            $safeFilename = $this->validateFilename($primaryFile['filename']);

                            $type = ModrinthProjectType::fromServer($server);
                            if (!$type) {
                                throw new Exception('Server does not support Modrinth mods or plugins');
                            }

                            $fileRepository->setServer($server)->pull($primaryFile['url'], $type->getFolder());

                            $saved = MinecraftModrinth::saveModMetadata(
                                $server,
                                $fileRepository,
                                $record['project_id'],
                                $record['slug'],
                                $record['title'],
                                $latestVersion['id'],
                                $latestVersion['version_number'],
                                $safeFilename
                            );

                            if (!$saved) {
                                // Rollback: delete the downloaded file to maintain consistency
                                try {
                                    Http::daemon($server->node)
                                        ->post("/api/servers/{$server->uuid}/files/delete", [
                                            'root' => '/',
                                            'files' => [$type->getFolder() . '/' . $safeFilename],
                                        ])
                                        ->throw();
                                } catch (Exception $rollbackException) {
                                    // Log rollback failure but continue with the original exception
                                    report($rollbackException);
                                }

                                throw new Exception('Failed to save mod metadata');
                            }

                            $this->installedModsMetadata = null;
                            $this->versionsCache = [];

                            Notification::make()
                                ->title(trans('minecraft-modrinth::strings.notifications.install_success'))
                                ->body(trans('minecraft-modrinth::strings.notifications.install_success_body', [
                                    'name' => $record['title'],
                                    'version' => $latestVersion['version_number'],
                                ]))
                                ->success()
                                ->send();
                        } catch (Exception $exception) {
                            report($exception);

                            $this->installedModsMetadata = null;
                            $this->versionsCache = [];

                            Notification::make()
                                ->title(trans('minecraft-modrinth::strings.notifications.install_failed'))
                                ->body(trans('minecraft-modrinth::strings.notifications.install_failed_body'))
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('update')
                    ->iconButton()
                    ->icon('tabler-refresh')
                    ->color('warning')
                    ->tooltip(trans('minecraft-modrinth::strings.actions.update'))
                    ->visible(function (array $record) {
                        $installedMod = $this->getInstalledMod($record['project_id']);

                        if (is_null($installedMod)) {
                            return false;
                        }

                        $versions = $this->getCachedVersions($record['project_id']);

                        if (empty($versions)) {
                            return false;
                        }

                        return $installedMod['version_id'] !== $versions[0]['id'];
                    })
                    ->requiresConfirmation()
                    ->modalHeading(fn (array $record) => trans('minecraft-modrinth::strings.modals.update_heading'))
                    ->modalDescription(function (array $record) {
                        $installedMod = $this->getInstalledMod($record['project_id']);
                        $versions = $this->getCachedVersions($record['project_id']);

                        return trans('minecraft-modrinth::strings.modals.update_description', [
                            'old_version' => $installedMod['version_number'] ?? 'unknown',
                            'new_version' => $versions[0]['version_number'] ?? 'unknown',
                        ]);
                    })
                    ->action(function (array $record, DaemonFileRepository $fileRepository) {
                        try {
                            /** @var Server $server */
                            $server = Filament::getTenant();

                            $installedMod = $this->getInstalledMod($record['project_id']);

                            if (!$installedMod) {
                                throw new Exception('Mod not found in metadata');
                            }

                            $safeFilename = $this->validateFilename($installedMod['filename']);

                            $versions = MinecraftModrinth::getModrinthVersions($record['project_id'], $server);

                            if (empty($versions)) {
                                throw new Exception('No compatible versions found');
                            }

                            $latestVersion = $versions[0];

                            if (!isset($latestVersion['id'], $latestVersion['version_number'], $latestVersion['files'])) {
                                throw new Exception('Invalid version data structure');
                            }

                            $primaryFile = $this->getPrimaryFile($latestVersion['files']);

                            if (!$primaryFile) {
                                throw new Exception('No downloadable file found');
                            }

                            // Validate new filename from Modrinth API to prevent path traversal
                            $safeNewFilename = $this->validateFilename($primaryFile['filename']);

                            $type = ModrinthProjectType::fromServer($server);
                            if (!$type) {
                                throw new Exception('Server does not support Modrinth mods or plugins');
                            }

                            $folder = $type->getFolder();

                            // Download new version first to avoid leaving mod in broken state if download fails
                            $fileRepository->setServer($server)->pull($primaryFile['url'], $folder);

                            // Update metadata before deleting old file to maintain consistency
                            $saved = MinecraftModrinth::saveModMetadata(
                                $server,
                                $fileRepository,
                                $record['project_id'],
                                $record['slug'],
                                $record['title'],
                                $latestVersion['id'],
                                $latestVersion['version_number'],
                                $safeNewFilename
                            );

                            if (!$saved) {
                                // Rollback: delete the newly downloaded file to restore original state
                                try {
                                    Http::daemon($server->node)
                                        ->post("/api/servers/{$server->uuid}/files/delete", [
                                            'root' => '/',
                                            'files' => [$folder . '/' . $safeNewFilename],
                                        ])
                                        ->throw();
                                } catch (Exception $rollbackException) {
                                    // Log rollback failure but continue with the original exception
                                    report($rollbackException);
                                }

                                throw new Exception('Failed to save mod metadata');
                            }

                            // Only delete old version after successful metadata save (if filenames differ)
                            // If filenames are the same, the download already replaced the file
                            if ($safeFilename !== $safeNewFilename) {
                                Http::daemon($server->node)
                                    ->post("/api/servers/{$server->uuid}/files/delete", [
                                        'root' => '/',
                                        'files' => [$folder . '/' . $safeFilename],
                                    ])
                                    ->throw();
                            }

                            $this->installedModsMetadata = null;
                            $this->versionsCache = [];

                            Notification::make()
                                ->title(trans('minecraft-modrinth::strings.notifications.update_success'))
                                ->body(trans('minecraft-modrinth::strings.notifications.update_success_body', [
                                    'version' => $latestVersion['version_number'],
                                ]))
                                ->success()
                                ->send();
                        } catch (Exception $exception) {
                            report($exception);

                            $this->installedModsMetadata = null;
                            $this->versionsCache = [];

                            Notification::make()
                                ->title(trans('minecraft-modrinth::strings.notifications.update_failed'))
                                ->body(trans('minecraft-modrinth::strings.notifications.update_failed_body'))
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('installed')
                    ->iconButton()
                    ->icon('tabler-check')
                    ->color('success')
                    ->tooltip(trans('minecraft-modrinth::strings.actions.installed'))
                    ->disabled()
                    ->visible(function (array $record) {
                        $installedMod = $this->getInstalledMod($record['project_id']);

                        if (is_null($installedMod)) {
                            return false;
                        }

                        $versions = $this->getCachedVersions($record['project_id']);

                        if (empty($versions)) {
                            return true;
                        }

                        return $installedMod['version_id'] === $versions[0]['id'];
                    }),
                Action::make('uninstall')
                    ->iconButton()
                    ->icon('tabler-trash')
                    ->color('danger')
                    ->tooltip(trans('minecraft-modrinth::strings.actions.uninstall'))
                    ->visible(function (array $record) {
                        return !is_null($this->getInstalledMod($record['project_id']));
                    })
                    ->requiresConfirmation()
                    ->modalHeading(fn (array $record) => trans('minecraft-modrinth::strings.modals.uninstall_heading'))
                    ->modalDescription(fn (array $record) => trans('minecraft-modrinth::strings.modals.uninstall_description', ['name' => $record['title']]))
                    ->action(function (array $record, DaemonFileRepository $fileRepository) {
                        try {
                            /** @var Server $server */
                            $server = Filament::getTenant();

                            $installedMod = $this->getInstalledMod($record['project_id']);

                            if (!$installedMod) {
                                throw new Exception('Mod not found in metadata');
                            }

                            $safeFilename = $this->validateFilename($installedMod['filename']);

                            // Remove metadata first to maintain consistency
                            // If file deletion fails after this, the file will exist but won't be tracked
                            // which is safer than having metadata pointing to a non-existent file
                            $metadataRemoved = MinecraftModrinth::removeModMetadata($server, $fileRepository, $record['project_id']);

                            if ($metadataRemoved === false) {
                                throw new Exception('Failed to remove mod metadata');
                            }

                            $type = ModrinthProjectType::fromServer($server);
                            if (!$type) {
                                throw new Exception('Server does not support Modrinth mods or plugins');
                            }

                            $folder = $type->getFolder();

                            Http::daemon($server->node)
                                ->post("/api/servers/{$server->uuid}/files/delete", [
                                    'root' => '/',
                                    'files' => [$folder . '/' . $safeFilename],
                                ])
                                ->throw();

                            $this->installedModsMetadata = null;
                            $this->versionsCache = [];

                            Notification::make()
                                ->title(trans('minecraft-modrinth::strings.notifications.uninstall_success'))
                                ->body($record['title'])
                                ->success()
                                ->send();
                        } catch (Exception $exception) {
                            report($exception);

                            $this->installedModsMetadata = null;
                            $this->versionsCache = [];

                            Notification::make()
                                ->title(trans('minecraft-modrinth::strings.notifications.uninstall_failed'))
                                ->body(trans('minecraft-modrinth::strings.notifications.uninstall_failed_body'))
                                ->danger()
                                ->send();
                        }
                    }),
            ]);
    }

    protected function getHeaderActions(): array
    {
        /** @var Server $server */
        $server = Filament::getTenant();

        $type = ModrinthProjectType::fromServer($server);
        if (!$type) {
            return [];
        }

        $folder = $type->getFolder();

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

        $type = ModrinthProjectType::fromServer($server);

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
                            ->label(fn () => trans('minecraft-modrinth::strings.page.installed', ['type' => $type?->getLabel() ?? 'Modrinth']))
                            ->state(function (DaemonFileRepository $fileRepository) use ($server, $type) {
                                try {
                                    if (!$type) {
                                        return trans('minecraft-modrinth::strings.page.unknown');
                                    }

                                    $files = $fileRepository->setServer($server)->getDirectory($type->getFolder());

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
