<?php

namespace Boy132\PlayerCounter\Models;

use App\Models\Allocation;
use App\Models\Egg;
use App\Models\Server;
use Boy132\PlayerCounter\Extensions\Query\QueryTypeService;
use Boy132\PlayerCounter\Extensions\Query\ServerAwareQueryTypeSchemaInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $query_type
 * @property ?int $query_port_offset
 * @property ?string $query_port_variable
 * @property Collection|Egg[] $eggs
 * @property int|null $eggs_count
 */
class GameQuery extends Model
{
    protected $fillable = [
        'query_type',
        'query_port_offset',
        'query_port_variable',
    ];

    protected $attributes = [
        'query_port_offset' => null,
        'query_port_variable' => null,
    ];

    public function eggs(): BelongsToMany
    {
        return $this->belongsToMany(Egg::class);
    }

    /** @return ?array{hostname: string, map: string, current_players: int, max_players: int, players: ?array<array{id: string, name: string}>} */
    public function runQuery(Server $server): ?array
    {
        if (!static::canRunQuery($server->allocation)) {
            return null;
        }

        $ip = config('player-counter.use_alias') && is_ip($server->allocation->alias) ? $server->allocation->alias : $server->allocation->ip;
        $ip = is_ipv6($ip) ? '[' . $ip . ']' : $ip;

        $port = $server->allocation->port + ($this->query_port_offset ?? 0);

        if ($this->query_port_variable) {
            $variableValue = $server->variables()->where('env_variable', $this->query_port_variable)->first()?->server_value;

            if ($variableValue && is_numeric($variableValue)) {
                $port = (int) $variableValue;
            }
        }

        /** @var QueryTypeService $service */
        $service = app(QueryTypeService::class);

        $schema = $service->get($this->query_type);
        if ($schema instanceof ServerAwareQueryTypeSchemaInterface) {
            return $schema->processWithServer($server, $ip, $port);
        }

        return $schema?->process($ip, $port);
    }

    public static function canRunQuery(?Allocation $allocation): bool
    {
        if (!$allocation) {
            return false;
        }

        $ip = config('player-counter.use_alias') && is_ip($allocation->alias) ? $allocation->alias : $allocation->ip;

        return !in_array($ip, ['0.0.0.0', '::']);
    }
}
