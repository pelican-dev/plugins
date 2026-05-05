<?php

namespace Boy132\MinecraftModrinth\Facades;

use App\Models\Server;
use App\Repositories\Daemon\DaemonFileRepository;
use Boy132\MinecraftModrinth\Services\MinecraftModrinthService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static ?string getMinecraftVersion(Server $server)
 * @method static array{icon: string, name: string, supported_project_types: string[], display_name: string}|null getLoaderFromServer(Server $server)
 * @method static array<int, array{icon: string, name: string, supported_project_types: string[]}> getLoaders()
 * @method static array{hits: array<int, array<string, mixed>>, total_hits: int} getProjects(Server $server, int $page = 1, ?string $search = null)
 * @method static array<int, array<string, mixed>> getInstalledModsFromModrinth(array $installedMods, int $page = 1)
 * @method static array<array{name: string, version_number: string, changelog: ?string, dependencies: array<mixed>, game_version: string[], version_type: string, loaders: string[], featured: bool, status: string, requested_status: ?string, id: string, project_id: string, author_id: string, date_published: string, downloads: int, changelog_url: ?string, files: array<mixed>}> getProjectVersions(string $projectId, Server $server)
 * @method static array<int, array{project_id: string, project_slug: string, project_title: string, version_id: string, version_number: string, filename: string, installed_at: string, author?: string}> getInstalledModsMetadata(Server $server, DaemonFileRepository $fileRepository)
 * @method static bool saveModMetadata(Server $server, DaemonFileRepository $fileRepository, string $projectId, string $projectSlug, string $projectTitle, string $versionId, string $versionNumber, string $filename, ?string $author = null)
 * @method static bool removeModMetadata(Server $server, DaemonFileRepository $fileRepository, string $projectId)
 * @method static array{project_id: string, project_slug: string, project_title: string, version_id: string, version_number: string, filename: string, installed_at: string, author?: string}|null getInstalledMod(Server $server, DaemonFileRepository $fileRepository, string $projectId)
 * @method static bool isUpdateAvailable(array $installedMod, array $availableVersions)
 * @method static array<string> getInstalledMods(Server $server, DaemonFileRepository $fileRepository)
 *
 * @see MinecraftModrinthService
 */
class MinecraftModrinth extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MinecraftModrinthService::class;
    }
}
