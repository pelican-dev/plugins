<?php

namespace spolny\ArmaReforgerWorkshop\Services;

use App\Models\Server;
use App\Repositories\Daemon\DaemonFileRepository;
use Exception;
use Illuminate\Support\Facades\Http;

class ArmaReforgerWorkshopService
{
    public const WORKSHOP_URL = 'https://reforger.armaplatform.com/workshop';

    public function isArmaReforgerServer(Server $server): bool
    {
        $features = $server->egg->features ?? [];
        $tags = $server->egg->tags ?? [];

        return in_array('arma_reforger_workshop', $features)
            || in_array('arma_reforger', $tags)
            || in_array('arma-reforger', $tags);
    }

    /**
     * Get installed mods from the server's config.json
     *
     * @return array<int, array{modId: string, name: string, version: string}>
     */
    public function getInstalledMods(Server $server, DaemonFileRepository $fileRepository): array
    {
        try {
            $configPath = $this->getConfigPath($server);
            $content = $fileRepository->setServer($server)->getContent($configPath);
            $config = json_decode($content, true);

            if (!$config || !isset($config['game']['mods'])) {
                return [];
            }

            return collect($config['game']['mods'])
                ->map(function ($mod) {
                    return [
                        'modId' => $mod['modId'] ?? '',
                        'name' => $mod['name'] ?? 'Unknown',
                        'version' => $mod['version'] ?? '',
                    ];
                })
                ->filter(fn ($mod) => !empty($mod['modId']))
                ->values()
                ->toArray();
        } catch (Exception $exception) {
            report($exception);

            return [];
        }
    }

    /**
     * Get the path to the server config.json
     */
    public function getConfigPath(Server $server): string
    {
        // Check for config path variable
        $configPath = $server->variables()
            ->where('env_variable', 'CONFIG_FILE')
            ->first()?->server_value;

        return $configPath ?? 'config.json';
    }

    /**
     * Add a mod to the server's config.json
     */
    public function addMod(Server $server, DaemonFileRepository $fileRepository, string $modId, string $name, string $version = ''): bool
    {
        try {
            $configPath = $this->getConfigPath($server);
            $content = $fileRepository->setServer($server)->getContent($configPath);
            $config = json_decode($content, true);

            if (!$config) {
                return false;
            }

            // Initialize mods array if it doesn't exist
            if (!isset($config['game'])) {
                $config['game'] = [];
            }
            if (!isset($config['game']['mods'])) {
                $config['game']['mods'] = [];
            }

            // Check if mod already exists
            foreach ($config['game']['mods'] as $existingMod) {
                if (($existingMod['modId'] ?? '') === $modId) {
                    return true; // Already installed
                }
            }

            // Add the new mod
            $newMod = [
                'modId' => $modId,
                'name' => $name,
            ];

            if (!empty($version)) {
                $newMod['version'] = $version;
            }

            $config['game']['mods'][] = $newMod;

            // Write back the config
            $fileRepository->setServer($server)->putContent(
                $configPath,
                json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            );

            return true;
        } catch (Exception $exception) {
            report($exception);

            return false;
        }
    }

    /**
     * Remove a mod from the server's config.json
     */
    public function removeMod(Server $server, DaemonFileRepository $fileRepository, string $modId): bool
    {
        try {
            $configPath = $this->getConfigPath($server);
            $content = $fileRepository->setServer($server)->getContent($configPath);
            $config = json_decode($content, true);

            if (!$config || !isset($config['game']['mods'])) {
                return false;
            }

            $config['game']['mods'] = collect($config['game']['mods'])
                ->filter(fn ($mod) => ($mod['modId'] ?? '') !== $modId)
                ->values()
                ->toArray();

            $fileRepository->setServer($server)->putContent(
                $configPath,
                json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            );

            return true;
        } catch (Exception $exception) {
            report($exception);

            return false;
        }
    }

    /**
     * Get the workshop URL for a mod
     */
    public function getModWorkshopUrl(string $modId): string
    {
        return self::WORKSHOP_URL . '/' . $modId;
    }

    /**
     * Fetch mod details from the workshop page
     *
     * @return array<string, mixed>
     */
    public function getModDetails(string $modId): array
    {
        return cache()->remember("arma_reforger_mod:$modId", now()->addHours(6), function () use ($modId) {
            try {
                $response = Http::timeout(10)
                    ->connectTimeout(5)
                    ->get(self::WORKSHOP_URL . '/' . $modId);

                if (!$response->successful()) {
                    return ['modId' => $modId];
                }

                $html = $response->body();

                $details = [
                    'modId' => $modId,
                ];

                // Extract data from __NEXT_DATA__ JSON
                if (preg_match('/<script[^>]*id="__NEXT_DATA__"[^>]*>(.+?)<\/script>/s', $html, $jsonMatch)) {
                    $jsonData = json_decode($jsonMatch[1], true);

                    if ($jsonData && isset($jsonData['props']['pageProps']['asset'])) {
                        $asset = $jsonData['props']['pageProps']['asset'];

                        $details['name'] = $asset['name'] ?? null;
                        $details['version'] = $asset['currentVersionNumber'] ?? null;
                        $details['subscribers'] = $asset['subscriberCount'] ?? null;

                        // Rating is stored as decimal (0.96 = 96%)
                        if (isset($asset['averageRating'])) {
                            $details['rating'] = (int) round($asset['averageRating'] * 100);
                        }
                    }

                    // Downloads are in a separate key
                    if (isset($jsonData['props']['pageProps']['getAssetDownloadTotal']['total'])) {
                        $details['downloads'] = $jsonData['props']['pageProps']['getAssetDownloadTotal']['total'];
                    }
                }

                return array_filter($details, fn ($v) => $v !== null);
            } catch (Exception $exception) {
                report($exception);

                return ['modId' => $modId];
            }
        });
    }

    /**
     * Search/browse mods from the Bohemia Workshop
     *
     * @return array{mods: array, total: int, page: int, perPage: int}
     */
    public function browseWorkshop(string $search = '', int $page = 1): array
    {
        $cacheKey = 'arma_reforger_browse:' . md5($search) . ":$page";

        return cache()->remember($cacheKey, now()->addMinutes(15), function () use ($search, $page) {
            try {
                $url = self::WORKSHOP_URL . '?' . http_build_query([
                    'search' => $search,
                    'page' => $page,
                ]);

                $response = Http::timeout(15)
                    ->connectTimeout(5)
                    ->get($url);

                if (!$response->successful()) {
                    return ['mods' => [], 'total' => 0, 'page' => $page, 'perPage' => 24];
                }

                $html = $response->body();
                $mods = [];
                $total = 0;

                // Extract data from __NEXT_DATA__ JSON
                if (preg_match('/<script[^>]*id="__NEXT_DATA__"[^>]*>(.+?)<\/script>/s', $html, $jsonMatch)) {
                    $jsonData = json_decode($jsonMatch[1], true);

                    if ($jsonData && isset($jsonData['props']['pageProps']['assets'])) {
                        $assets = $jsonData['props']['pageProps']['assets'];
                        $total = $assets['count'] ?? 0;

                        foreach ($assets['rows'] ?? [] as $asset) {
                            $mod = [
                                'modId' => $asset['id'] ?? '',
                                'name' => $asset['name'] ?? 'Unknown',
                                'summary' => $asset['summary'] ?? '',
                                'author' => $asset['author']['username'] ?? 'Unknown',
                                'version' => $asset['currentVersionNumber'] ?? '',
                                'subscribers' => $asset['subscriberCount'] ?? 0,
                                'rating' => isset($asset['averageRating']) ? (int) round($asset['averageRating'] * 100) : null,
                                'thumbnail' => $this->extractThumbnail($asset['previews'] ?? []),
                                'type' => $asset['type'] ?? 'addon',
                                'tags' => collect($asset['tags'] ?? [])->pluck('name')->toArray(),
                            ];

                            if (!empty($mod['modId'])) {
                                $mods[] = $mod;
                            }
                        }
                    }
                }

                return [
                    'mods' => $mods,
                    'total' => $total,
                    'page' => $page,
                    'perPage' => 24, // Workshop uses 24 per page
                ];
            } catch (Exception $exception) {
                report($exception);

                return ['mods' => [], 'total' => 0, 'page' => $page, 'perPage' => 24];
            }
        });
    }

    /**
     * Extract the best thumbnail URL from previews
     */
    protected function extractThumbnail(array $previews): ?string
    {
        if (empty($previews)) {
            return null;
        }

        $preview = $previews[0];

        // Try to get a smaller thumbnail for performance
        if (isset($preview['thumbnails']['image/jpeg'])) {
            $thumbnails = $preview['thumbnails']['image/jpeg'];
            // Get the smallest thumbnail (usually last one)
            if (!empty($thumbnails)) {
                return end($thumbnails)['url'] ?? $preview['url'] ?? null;
            }
        }

        return $preview['url'] ?? null;
    }

    /**
     * Check if a mod is already installed on the server
     */
    public function isModInstalled(Server $server, DaemonFileRepository $fileRepository, string $modId): bool
    {
        $installedMods = $this->getInstalledMods($server, $fileRepository);

        foreach ($installedMods as $mod) {
            if (strtoupper($mod['modId']) === strtoupper($modId)) {
                return true;
            }
        }

        return false;
    }
}
