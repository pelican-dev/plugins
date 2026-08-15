<?php

namespace Boy132\UserCreatableServers\Models;

use App\Models\Egg;
use App\Models\Objects\DeploymentObject;
use App\Models\Server;
use App\Models\User;
use App\Services\Servers\ServerCreationService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property User $user
 * @property int $user_id
 * @property int $cpu
 * @property int $memory
 * @property int $disk
 * @property ?int $server_limit
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class UserResourceLimits extends Model
{
    protected $fillable = [
        'user_id',
        'cpu',
        'memory',
        'disk',
        'server_limit',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getCpuLeft(): ?int
    {
        $userCpuLeft = null;

        if ($this->cpu > 0) {
            $sum_cpu = $this->user->servers->sum('cpu');

            $userCpuLeft = (int) max(0, $this->cpu - $sum_cpu);
        }

        return $this->getLowestLimit($userCpuLeft, $this->getUcsResourceLeft('cpu'));
    }

    public function getMemoryLeft(): ?int
    {
        $userMemoryLeft = null;

        if ($this->memory > 0) {
            $sum_memory = $this->user->servers->sum('memory');

            $userMemoryLeft = (int) max(0, $this->memory - $sum_memory);
        }

        return $this->getLowestLimit($userMemoryLeft, $this->getUcsResourceLeft('memory'));
    }

    public function getDiskLeft(): ?int
    {
        $userDiskLeft = null;

        if ($this->disk > 0) {
            $sum_disk = $this->user->servers->sum('disk');

            $userDiskLeft = (int) max(0, $this->disk - $sum_disk);
        }

        return $this->getLowestLimit($userDiskLeft, $this->getUcsResourceLeft('disk'));
    }

    public function canUpdateServerResources(Server $server, int $cpu, int $memory, int $disk): bool
    {
        return $this->canAllocateResources($cpu, $memory, $disk, $server);
    }

    public function updateServerResources(Server $server, int $cpu, int $memory, int $disk): Server|false
    {
        return DB::transaction(function () use ($server, $cpu, $memory, $disk) {
            $userResourceLimits = $this->lockQuotaRecord();
            $server = Server::query()->lockForUpdate()->findOrFail($server->getKey());

            if (!$userResourceLimits->canUpdateServerResources($server, $cpu, $memory, $disk)) {
                return false;
            }

            $server->update([
                'cpu' => $cpu,
                'memory' => $memory,
                'disk' => $disk,
            ]);

            return $server;
        }, 5);
    }

    public function canCreateServer(int $cpu, int $memory, int $disk): bool
    {
        if ($this->server_limit && $this->user->servers->count() >= $this->server_limit) {
            return false;
        }

        return $this->canAllocateResources($cpu, $memory, $disk);
    }

    private function canAllocateResources(int $cpu, int $memory, int $disk, ?Server $excludedServer = null): bool
    {
        foreach (['cpu' => $cpu, 'memory' => $memory, 'disk' => $disk] as $resource => $requested) {
            $userLimit = $this->{$resource};

            if ($userLimit > 0 && ($requested <= 0 || $this->getUserAllocatedResource($resource, $excludedServer) + $requested > $userLimit)) {
                return false;
            }

            $limit = (int) config("user-creatable-servers.max_{$resource}");

            if ($limit > 0 && $this->getUcsAllocatedResource($resource, $excludedServer) + $requested > $limit) {
                return false;
            }
        }

        return true;
    }

    private function getUcsResourceLeft(string $resource): ?int
    {
        $limit = (int) config("user-creatable-servers.max_{$resource}");

        if ($limit <= 0) {
            return null;
        }

        return (int) max(0, $limit - $this->getUcsAllocatedResource($resource));
    }

    private function getUcsAllocatedResource(string $resource, ?Server $excludedServer = null): int
    {
        $servers = Server::query()->whereIn('owner_id', self::query()->select('user_id'));

        if ($excludedServer) {
            $servers->whereKeyNot($excludedServer->getKey());
        }

        return (int) $servers->sum($resource);
    }

    private function getUserAllocatedResource(string $resource, ?Server $excludedServer = null): int
    {
        $servers = Server::query()->where('owner_id', $this->user_id);

        if ($excludedServer) {
            $servers->whereKeyNot($excludedServer->getKey());
        }

        return (int) $servers->sum($resource);
    }

    private function lockQuotaRecord(): self
    {
        self::query()->orderBy('id')->lockForUpdate()->firstOrFail();

        return self::query()->findOrFail($this->getKey());
    }

    private function getLowestLimit(?int ...$limits): ?int
    {
        $limits = array_filter($limits, fn (?int $limit) => $limit !== null);

        return empty($limits) ? null : min($limits);
    }

    /** @param array<string, mixed> $variables */
    public function createServer(string $name, int|Egg $egg, int $cpu, int $memory, int $disk, array $variables = []): Server|false
    {
        return DB::transaction(function () use ($name, $egg, $cpu, $memory, $disk, $variables) {
            $userResourceLimits = $this->lockQuotaRecord();

            if (!$userResourceLimits->canCreateServer($cpu, $memory, $disk)) {
                return false;
            }

            if (!$egg instanceof Egg) {
                $egg = Egg::findOrFail($egg);
            }

            $environment = [];
            foreach ($egg->variables as $variable) {
                $environment[$variable->env_variable] = $variables[$variable->env_variable] ?? $variable->default_value;
            }

            $data = [
                'name' => $name,
                'owner_id' => $this->user_id,
                'egg_id' => $egg->id,
                'cpu' => $cpu,
                'memory' => $memory,
                'disk' => $disk,
                'swap' => 0,
                'io' => 500,
                'environment' => $environment,
                'skip_scripts' => false,
                'start_on_completion' => true,
                'oom_killer' => false,
                'database_limit' => config('user-creatable-servers.database_limit'),
                'allocation_limit' => config('user-creatable-servers.allocation_limit'),
                'backup_limit' => config('user-creatable-servers.backup_limit'),
            ];

            $object = new DeploymentObject();
            $object->setDedicated(false);
            $object->setTags(array_filter(explode(',', config('user-creatable-servers.deployment_tags'))));
            $object->setPorts(array_filter(explode(',', config('user-creatable-servers.deployment_ports'))));

            /** @var ServerCreationService $service */
            $service = app(ServerCreationService::class);

            return $service->handle($data, $object);
        }, 5);
    }
}
