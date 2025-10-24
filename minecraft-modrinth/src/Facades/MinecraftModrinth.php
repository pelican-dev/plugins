<?php

namespace Boy132\MinecraftModrinth\Facades;

use App\Models\Server;
use Boy132\MinecraftModrinth\Enums\ModrinthProjectType;
use Boy132\MinecraftModrinth\Services\MinecraftModrinthService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static ?string getMinecraftVersion(Server $server)
 * @method static ?string getMinecraftLoader(Server $server)
 * @method static ?ModrinthProjectType getModrinthProjectType(Server $server)
 * @method static array{hits: array<int, array<string, mixed>>, total_hits: int} getModrinthProjects(Server $server, int $page = 1, ?string $search = null)
 * @method static array<int, mixed> getModrinthVersions(string $projectId, Server $server)
 *
 * @see \Boy132\MinecraftModrinth\MinecraftModrinthService
 */
class MinecraftModrinth extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MinecraftModrinthService::class;
    }
}
