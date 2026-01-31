<?php

namespace Boy132\MinecraftModrinth\Services;

use App\Models\Server;
use App\Repositories\Daemon\DaemonFileRepository;
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

    /**
     * @throws Exception
     */
    protected function getMetadataFilePath(Server $server): string
    {
        $type = ModrinthProjectType::fromServer($server);

        if (!$type) {
            throw new Exception("Server {$server->id} does not support Modrinth mods or plugins");
        }

        return $type->getFolder() . '/.modrinth-metadata.json';
    }

    /** @return array<int, array{project_id: string, project_slug: string, project_title: string, version_id: string, version_number: string, filename: string, installed_at: string}> */
    public function getInstalledModsMetadata(Server $server, DaemonFileRepository $fileRepository): array
    {
        try {
            $metadataPath = $this->getMetadataFilePath($server);
            $content = $fileRepository->setServer($server)->getContent($metadataPath);
            $metadata = json_decode($content, true);

            if (!is_array($metadata) || !isset($metadata['installed_mods']) || !is_array($metadata['installed_mods'])) {
                return [];
            }

            $validInstalledMods = [];
            $requiredKeys = [
                'project_id',
                'project_slug',
                'project_title',
                'version_id',
                'version_number',
                'filename',
                'installed_at',
            ];

            // Flip once for efficient comparison
            $requiredKeysFlipped = array_flip($requiredKeys);

            foreach ($metadata['installed_mods'] as $entry) {
                if (!is_array($entry)) {
                    continue;
                }

                $missingKeys = array_diff_key($requiredKeysFlipped, $entry);
                if (empty($missingKeys)) {
                    $validInstalledMods[] = $entry;
                }
            }

            return $validInstalledMods;
        } catch (Exception $exception) {
            // File doesn't exist yet or is invalid, return empty array
            return [];
        }
    }

    public function saveModMetadata(
        Server $server,
        DaemonFileRepository $fileRepository,
        string $projectId,
        string $projectSlug,
        string $projectTitle,
        string $versionId,
        string $versionNumber,
        string $filename
    ): bool {
        try {
            $metadata = [
                'installed_mods' => $this->getInstalledModsMetadata($server, $fileRepository),
            ];

            // Remove any existing entry for this project
            $metadata['installed_mods'] = collect($metadata['installed_mods'])
                ->filter(fn ($mod) => $mod['project_id'] !== $projectId)
                ->values()
                ->toArray();

            // Add new entry
            $metadata['installed_mods'][] = [
                'project_id' => $projectId,
                'project_slug' => $projectSlug,
                'project_title' => $projectTitle,
                'version_id' => $versionId,
                'version_number' => $versionNumber,
                'filename' => $filename,
                'installed_at' => now()->toIso8601String(),
            ];

            $metadataPath = $this->getMetadataFilePath($server);
            $response = $fileRepository->setServer($server)->putContent(
                $metadataPath,
                json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            );

            if ($response->failed()) {
                return false;
            }

            return true;
        } catch (Exception $exception) {
            report($exception);

            return false;
        }
    }

    public function removeModMetadata(Server $server, DaemonFileRepository $fileRepository, string $projectId): bool
    {
        try {
            $metadata = [
                'installed_mods' => $this->getInstalledModsMetadata($server, $fileRepository),
            ];

            $metadata['installed_mods'] = collect($metadata['installed_mods'])
                ->filter(fn ($mod) => $mod['project_id'] !== $projectId)
                ->values()
                ->toArray();

            $metadataPath = $this->getMetadataFilePath($server);
            $response = $fileRepository->setServer($server)->putContent(
                $metadataPath,
                json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            );

            if ($response->failed()) {
                return false;
            }

            return true;
        } catch (Exception $exception) {
            report($exception);

            return false;
        }
    }

    /** @return array{project_id: string, project_slug: string, project_title: string, version_id: string, version_number: string, filename: string, installed_at: string}|null */
    public function getInstalledMod(Server $server, DaemonFileRepository $fileRepository, string $projectId): ?array
    {
        $installedMods = $this->getInstalledModsMetadata($server, $fileRepository);

        foreach ($installedMods as $mod) {
            if ($mod['project_id'] === $projectId) {
                return $mod;
            }
        }

        return null;
    }

    /**
     * @param  array{version_id: string, version_number: string}  $installedMod
     * @param  array<int, array{id: string, version_number: string}>  $availableVersions
     */
    public function isUpdateAvailable(array $installedMod, array $availableVersions): bool
    {
        if (empty($availableVersions)) {
            return false;
        }

        $latestVersion = $availableVersions[0];

        return $installedMod['version_id'] !== $latestVersion['id'];
    }

    /**
     * Get installed mods/plugins filenames from the server (for backward compatibility)
     *
     * @return array<string>
     */
    public function getInstalledMods(Server $server, DaemonFileRepository $fileRepository): array
    {
        $metadata = $this->getInstalledModsMetadata($server, $fileRepository);

        return collect($metadata)
            ->pluck('filename')
            ->map(fn ($name) => strtolower($name))
            ->toArray();
    }
}
