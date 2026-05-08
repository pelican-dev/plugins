<?php

namespace Boy132\MinecraftModrinth\Facades;

use App\Models\Server;
use Boy132\MinecraftModrinth\Services\MinecraftModrinthService;
use Illuminate\Support\Facades\Facade;

/**
 * @phpstan-type InstalledModMetadata array{project_id: string, project_slug: string, project_title: string, version_id: string, version_number: string, filename: string, installed_at: string, author?: string}
 * @phpstan-type ModrinthVersion array{name: string, version_number: string, changelog: ?string, dependencies: array<mixed>, game_version: array<string>, version_type: string, loaders: array<string>, featured: bool, status: string, requested_status: ?string, id: string, project_id: string, author_id: string, date_published: string, downloads: int, changelog_url: ?string, files: array<mixed>}
 *
 * @method static ?string getMinecraftVersion(Server $server)
 * @method static array{icon: string, name: string, supported_project_types: string[], display_name: string}|null getLoaderFromServer(Server $server)
 * @method static array<int, array{icon: string, name: string, supported_project_types: string[]}> getLoaders()
 * @method static array{hits: array<int, array<string, mixed>>, total_hits: int} getProjects(Server $server, int $page = 1, ?string $search = null)
 * @method static array<int, array<string, mixed>> getInstalledModsFromModrinth(array<int, InstalledModMetadata> $installedMods, int $page = 1)
 * @method static array<int, ModrinthVersion> getProjectVersions(string $projectId, Server $server)
 * @method static array<int, InstalledModMetadata> getInstalledModsMetadata(Server $server)
 * @method static bool saveModMetadata(Server $server, string $projectId, string $projectSlug, string $projectTitle, string $versionId, string $versionNumber, string $filename, ?string $author = null)
 * @method static bool removeModMetadata(Server $server, string $projectId)
 * @method static InstalledModMetadata|null getInstalledMod(Server $server, string $projectId)
 * @method static bool isUpdateAvailable(array{version_id: string, version_number: string} $installedMod, array<int, array{id: string, version_number: string}> $availableVersions)
 * @method static array<string> getInstalledMods(Server $server)
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
