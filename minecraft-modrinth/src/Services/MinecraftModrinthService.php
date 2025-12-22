<?php

namespace Boy132\MinecraftModrinth\Services;

use App\Models\Server;
use Boy132\MinecraftModrinth\Enums\ModrinthProjectType;
use Exception;
use Illuminate\Support\Facades\Http;

class MinecraftModrinthService
{
    public function getMinecraftVersion(Server $server): ?string
    {
        $loader = $this->getMinecraftLoader($server);

        if ($loader === 'velocity') {
            return config('minecraft-modrinth.latest_minecraft_version');
        }

        $version = $server->variables()
            ->whereIn('env_variable', [
                'MINECRAFT_VERSION',
                'MC_VERSION',
                'VERSION',
                'SERVER_VERSION',
            ])
            ->first()?->server_value;

        if (!$version || $version === 'latest') {
            return config('minecraft-modrinth.latest_minecraft_version');
        }

        return $version;
    }

    public function getMinecraftLoader(Server $server): ?string
    {
        $tags = $server->egg->tags ?? [];

        if (in_array('minecraft', $tags)) {

            if (in_array('velocity', $tags)) {
                return 'velocity';
            }

            if (in_array('neoforge', $tags) || in_array('neoforged', $tags)) {
                return 'neoforge';
            }

            if (in_array('forge', $tags)) {
                return 'forge';
            }

            if (in_array('fabric', $tags)) {
                return 'fabric';
            }

            if (in_array('spigot', $tags) || in_array('paper', $tags)) {
                return 'paper';
            }
        }

        return null;
    }

    public function getModrinthProjectType(Server $server): ?ModrinthProjectType
    {
        $features = $server->egg->features ?? [];
        $tags = $server->egg->tags ?? [];

        if (in_array('modrinth_plugins', $features) || (in_array('minecraft', $tags) && in_array('plugins', $features))) {
            return ModrinthProjectType::Plugin;
        }

        if (in_array('modrinth_mods', $features) || (in_array('minecraft', $tags) && in_array('mods', $features))) {
            return ModrinthProjectType::Mod;
        }

        return null;
    }

    /** @return array{hits: array<int, array<string, mixed>>, total_hits: int} */
    public function getModrinthProjects(Server $server, int $page = 1, ?string $search = null): array
    {
        $projectType = $this->getModrinthProjectType($server);

        if (!$projectType) {
            return [
                'hits' => [],
                'total_hits' => 0,
            ];
        }

        $minecraftVersion = $this->getMinecraftVersion($server);
        $minecraftLoader = $this->getMinecraftLoader($server);

        $data = [
            'offset' => ($page - 1) * 20,
            'limit' => 20,
            'facets' => "[[\"categories:$minecraftLoader\"],[\"versions:$minecraftVersion\"],[\"project_type:{$projectType->value}\"]]",
        ];

        $key = "modrinth_projects:{$projectType->value}:$minecraftVersion:$minecraftLoader:$page";

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
        $minecraftVersion = $this->getMinecraftVersion($server);
        $minecraftLoader = $this->getMinecraftLoader($server);

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
