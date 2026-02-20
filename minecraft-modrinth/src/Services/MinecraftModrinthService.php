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

    /**
     * @param  array<int, array{project_id: string, project_slug: string, project_title: string, version_id: string, version_number: string, filename: string, installed_at: string, author?: string}>  $installedMods
     * @return array<int, array<string, mixed>>
     */
    public function getInstalledModsFromModrinth(array $installedMods, int $page = 1): array
    {
        if (empty($installedMods)) {
            return [];
        }

        $projectIds = collect($installedMods)->pluck('project_id')->unique()->values()->all();

        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        $pageIds = array_slice($projectIds, $offset, $perPage);

        if (empty($pageIds)) {
            return [];
        }

        $idsParam = '["' . implode('","', $pageIds) . '"]';
        $key = 'modrinth_bulk:' . md5($idsParam);

        $modrinthProjects = cache()->remember($key, now()->addMinutes(30), function () use ($idsParam) {
            try {
                return Http::asJson()
                    ->timeout(10)
                    ->connectTimeout(5)
                    ->throw()
                    ->get('https://api.modrinth.com/v2/projects', [
                        'ids' => $idsParam,
                    ])
                    ->json();
            } catch (Exception $exception) {
                report($exception);

                return [];
            }
        });

        if (!is_array($modrinthProjects)) {
            $modrinthProjects = [];
        }

        $modrinthMap = [];
        foreach ($modrinthProjects as $project) {
            if (isset($project['id'])) {
                $modrinthMap[$project['id']] = $project;
            }
        }

        $results = [];
        foreach ($pageIds as $projectId) {
            $installedMod = null;
            foreach ($installedMods as $mod) {
                if ($mod['project_id'] === $projectId) {
                    $installedMod = $mod;
                    break;
                }
            }

            if (!$installedMod) {
                continue;
            }

            if (isset($modrinthMap[$projectId])) {
                $project = $modrinthMap[$projectId];
                $project['project_id'] = $project['id'];
                if (isset($project['updated']) && !isset($project['date_modified'])) {
                    $project['date_modified'] = $project['updated'];
                }
                if (isset($installedMod['author']) && !isset($project['author'])) {
                    $project['author'] = $installedMod['author'];
                }
                $results[] = $project;
            } else {
                $results[] = [
                    'project_id' => $installedMod['project_id'],
                    'slug' => $installedMod['project_slug'],
                    'title' => $installedMod['project_title'],
                    'description' => trans('minecraft-modrinth::strings.page.mod_unavailable'),
                    'icon_url' => null,
                    'author' => $installedMod['author'] ?? '',
                    'downloads' => 0,
                    'date_modified' => $installedMod['installed_at'],
                    'project_type' => '',
                    'unavailable' => true,
                ];
            }
        }

        return $results;
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
                $versions = Http::asJson()
                    ->timeout(5)
                    ->connectTimeout(5)
                    ->throw()
                    ->get("https://api.modrinth.com/v2/project/$projectId/version", $data)
                    ->json();

                if (!empty($versions) && is_array($versions) && isset($versions[0]['date_published'])) {
                    usort($versions, function ($a, $b) {
                        return strcmp($b['date_published'] ?? '', $a['date_published'] ?? '');
                    });
                }

                return $versions;
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

    /** @return array<int, array{project_id: string, project_slug: string, project_title: string, version_id: string, version_number: string, filename: string, installed_at: string, author?: string}> */
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
        string $filename,
        ?string $author = null
    ): bool {
        try {
            $metadata = [
                'installed_mods' => $this->getInstalledModsMetadata($server, $fileRepository),
            ];

            $metadata['installed_mods'] = collect($metadata['installed_mods'])
                ->filter(fn ($mod) => $mod['project_id'] !== $projectId)
                ->values()
                ->toArray();

            $modEntry = [
                'project_id' => $projectId,
                'project_slug' => $projectSlug,
                'project_title' => $projectTitle,
                'version_id' => $versionId,
                'version_number' => $versionNumber,
                'filename' => $filename,
                'installed_at' => now()->toIso8601String(),
            ];

            if ($author !== null) {
                $modEntry['author'] = $author;
            }

            $metadata['installed_mods'][] = $modEntry;

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

    /** @return array{project_id: string, project_slug: string, project_title: string, version_id: string, version_number: string, filename: string, installed_at: string, author?: string}|null */
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
