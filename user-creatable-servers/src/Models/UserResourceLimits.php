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
        if ($this->cpu > 0) {
            $sum_cpu = $this->user->servers->sum('cpu');

            return max(0, $this->cpu - $sum_cpu);
        }

        return null;
    }

    public function getMemoryLeft(): ?int
    {
        if ($this->memory > 0) {
            $sum_memory = $this->user->servers->sum('memory');

            return max(0, $this->memory - $sum_memory);
        }

        return null;
    }

    public function getDiskLeft(): ?int
    {
        if ($this->disk > 0) {
            $sum_disk = $this->user->servers->sum('disk');

            return max(0, $this->disk - $sum_disk);
        }

        return null;
    }

    public function canCreateServer(int $cpu, int $memory, int $disk): bool
    {
        if ($this->server_limit && $this->user->servers->count() >= $this->server_limit) {
            return false;
        }

        if ($this->cpu > 0) {
            if ($cpu <= 0) {
                return false;
            }

            $sum_cpu = $this->user->servers->sum('cpu');
            if ($sum_cpu + $cpu > $this->cpu) {
                return false;
            }
        }

        if ($this->memory > 0) {
            if ($memory <= 0) {
                return false;
            }

            $sum_memory = $this->user->servers->sum('memory');
            if ($sum_memory + $memory > $this->memory) {
                return false;
            }
        }

        if ($this->disk > 0) {
            if ($disk <= 0) {
                return false;
            }

            $sum_disk = $this->user->servers->sum('disk');
            if ($sum_disk + $disk > $this->disk) {
                return false;
            }
        }

        return true;
    }

    public function createServer(string $name, int|Egg $egg, int $cpu, int $memory, int $disk): Server|bool
    {
        if ($this->canCreateServer($cpu, $memory, $disk)) {
            if (!$egg instanceof Egg) {
                $egg = Egg::findOrFail($egg);
            }

            $environment = [];
            foreach ($egg->variables as $variable) {
                $environment[$variable->env_variable] = $variable->default_value;
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
            $service = app(ServerCreationService::class); // @phpstan-ignore-line

            return $service->handle($data, $object);
        }

        return false;
    }
}
