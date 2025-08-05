<?php

namespace Boy132\PlayerCounter\Models;

use App\Models\Allocation;
use App\Models\Egg;
use GameQ\GameQ;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $query_type
 * @property ?int $query_port_offset
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Egg[] $eggs
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

    /** @return array<mixed> */
    public function runQuery(Allocation $allocation): array
    {
        $ip = is_ipv6($allocation->ip) ? '[' . $allocation->ip . ']' : $allocation->ip;
        $host = $ip . ':' . ($allocation->port + ($this->query_port_offset ?? 0));

        $gameQ = new GameQ();

        $gameQ->addServer([
            'type' => $this->query_type,
            'host' => $host,
        ]);

        return $gameQ->process()[$host] ?? [];
    }
}
