<?php

namespace Boy132\RustUMod\Services;

use App\Models\Server;
use Exception;
use Illuminate\Support\Facades\Http;

class RustUModService
{
    public function getRustModdingFramework(Server $server): ?string
    {
        return $server->variables()->where('env_variable', 'FRAMEWORK')->first()?->server_value;
    }

    public function isRustServer(Server $server): bool
    {
        $framework = $this->getRustModdingFramework($server);

        if (!in_array($framework, ['oxide', 'umod'])) {
            return false;
        }

        $server->loadMissing('egg');

        $features = $server->egg->features ?? [];
        $tags = $server->egg->tags ?? [];

        return in_array('umod_plugins', $features) || in_array('rust', $tags);
    }

    /** @return array{data: array<int, array<string, mixed>>, total: int} */
    public function getUModPlugins(int $page = 1, string $search = ''): array
    {
        return cache()->remember("umod_plugins:$page:$search", now()->addMinutes(30), function () use ($search, $page) {
            try {
                return Http::asJson()
                    ->timeout(5)
                    ->connectTimeout(5)
                    ->throw()
                    ->get("https://umod.org/plugins/search.json?page=$page&query=$search&sort=downloads&sortdir=desc&categories=universal%2Crust")
                    ->json();
            } catch (Exception $exception) {
                report($exception);

                return [
                    'data' => [],
                    'total' => 0,
                ];
            }
        });
    }

    /** @return array<string, mixed> */
    public function getUModPlugin(string $pluginName): array
    {
        return cache()->remember("umod_plugin:$pluginName", now()->addMinutes(30), function () use ($pluginName) {
            try {
                return Http::asJson()
                    ->timeout(5)
                    ->connectTimeout(5)
                    ->throw()
                    ->get("https://umod.org/plugins/$pluginName.json")
                    ->json();
            } catch (Exception $exception) {
                report($exception);

                return [];
            }
        });
    }
}
