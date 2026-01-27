<?php

namespace Boy132\PlayerCounter\Models;

use App\Models\Allocation;
use App\Models\Egg;
use Boy132\PlayerCounter\Extensions\Query\QueryTypeService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $query_type
 * @property ?int $query_port_offset
 * @property Collection|Egg[] $eggs
 * @property int|null $eggs_count
 */
class GameQuery extends Model
{
    protected $fillable = [
        'query_type',
        'query_port_offset',
    ];

    protected $attributes = [
        'query_port_offset' => null,
    ];

    public function eggs(): BelongsToMany
    {
        return $this->belongsToMany(Egg::class);
    }

    /** @return ?array{hostname: string, map: string, current_players: int, max_players: int, players: ?array<array{id: string, name: string}>} */
    public function runQuery(Allocation $allocation): ?array
    {
        $ip = config('player-counter.use_alias') && is_ip($allocation->alias) ? $allocation->alias : $allocation->ip;
        $ip = is_ipv6($ip) ? '[' . $ip . ']' : $ip;

        /** @var QueryTypeService $service */
        $service = app(QueryTypeService::class); // @phpstan-ignore myCustomRules.forbiddenGlobalFunctions

        return $service->get($this->query_type)->process($ip, $allocation->port + ($this->query_port_offset ?? 0));
    }
}
