<?php

namespace spolny\ArmaReforgerWorkshop\Facades;

use App\Models\Server;
use App\Repositories\Daemon\DaemonFileRepository;
use Illuminate\Support\Facades\Facade;
use spolny\ArmaReforgerWorkshop\Services\ArmaReforgerWorkshopService;

/**
 * @method static bool isArmaReforgerServer(Server $server)
 * @method static array<int, array{modId: string, name: string, version: string}> getInstalledMods(Server $server, DaemonFileRepository $fileRepository)
 * @method static string getConfigPath(Server $server)
 * @method static bool addMod(Server $server, DaemonFileRepository $fileRepository, string $modId, string $name, string $version = '')
 * @method static bool removeMod(Server $server, DaemonFileRepository $fileRepository, string $modId)
 * @method static string getModWorkshopUrl(string $modId)
 * @method static array<string, mixed> parseNextDataFromHtml(string $html, string $modId)
 * @method static array{mods: array<int, mixed>, total: int, page: int, perPage: int} browseWorkshop(string $search = '', int $page = 1)
 * @method static bool isModInstalled(Server $server, DaemonFileRepository $fileRepository, string $modId)
 *
 * @see ArmaReforgerWorkshopService
 */
class ArmaReforgerWorkshop extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ArmaReforgerWorkshopService::class;
    }
}
