<?php

namespace Boy132\MinecraftModrinth\Services;

use App\Models\Server;
use Boy132\MinecraftModrinth\Enums\MinecraftLoader;
use Boy132\MinecraftModrinth\Enums\ModrinthProjectType;
use Exception;
use Illuminate\Support\Facades\Http;

class MinecraftModrinthService
{
    public function getMinecraftVersion(Server $server): ?string
    {
        $version = $server->variables()->where(fn ($builder) => $builder->where('env_variable', 'MINECRAFT_VERSION')->orWhere('env_variable', 'MC_VERSION'))->first()?->server_value;

        if (!$version || $version === 'latest') {
            return config('minecraft-modrinth.latest_minecraft_version');
        }

        return $version;
    }

    /** @return array{hits: array<int, array<string, mixed>>, total_hits: int} */
    public function getModrinthProjects(Server $server, int $page = 1, ?string $search = null): array
    {
        $projectType = ModrinthProjectType::fromServer($server)?->value;
        $minecraftLoader = MinecraftLoader::fromServer($server)?->value;

        if (!$projectType || !$minecraftLoader) {
            return [
                'hits' => [],
                'total_hits' => 0,
            ];
        }

        $minecraftVersion = $this->getMinecraftVersion($server);

        $data = [
            'offset' => ($page - 1) * 20,
            'limit' => 20,
            'facets' => "[[\"categories:$minecraftLoader\"],[\"versions:$minecraftVersion\"],[\"project_type:{$projectType}\"]]",
        ];

        $key = "modrinth_projects:{$projectType}:$minecraftVersion:$minecraftLoader:$page";

        if ($search) {
            $data['query'] = $search;

            $key .= ":$search";
        }

        return cache()->remember($key, now()->addMinutes(30), function () use ($data) {
            try {
                return Http::asJson()
                    ->timeout(5)
                    ->connectTimeout(5)
                    ->throw()
                    ->get('https://api.modrinth.com/v2/search', $data)
                    ->json();
            } catch (Exception $exception) {
                report($exception);

                return [
                    'hits' => [],
                    'total_hits' => 0,
                ];
            }
        });
    }

    /** @return array<int, mixed> */
    public function getModrinthVersions(string $projectId, Server $server): array
    {
        $minecraftLoader = MinecraftLoader::fromServer($server)?->value;

        if (!$minecraftLoader) {
            return [];
        }

        $minecraftVersion = $this->getMinecraftVersion($server);

        $data = [
            'game_versions' => "[\"$minecraftVersion\"]",
            'loaders' => "[\"$minecraftLoader\"]",
        ];

        return cache()->remember("modrinth_versions:$projectId:$minecraftVersion:$minecraftLoader", now()->addMinutes(30), function () use ($projectId, $data) {
            try {
                return Http::asJson()
                    ->timeout(5)
                    ->connectTimeout(5)
                    ->throw()
                    ->get("https://api.modrinth.com/v2/project/$projectId/version", $data)
                    ->json();
            } catch (Exception $exception) {
                report($exception);

                return [];
            }
        });
    }
}
