<?php

namespace Boy132\PlayerCounter\Models;

use App\Models\Allocation;
use App\Models\Egg;
use Boy132\PlayerCounter\Enums\GameQueryType;
use Exception;
use GameQ\GameQ;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property GameQueryType $query_type
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

    protected function casts(): array
    {
        return [
            'query_type' => GameQueryType::class,
        ];
    }

    public function eggs(): BelongsToMany
    {
        return $this->belongsToMany(Egg::class);
    }

    /** @return array<string, mixed> */
    public function runQuery(Allocation $allocation): array
    {
        $ip = is_ipv6($allocation->ip) ? '[' . $allocation->ip . ']' : $allocation->ip;
        $port = $allocation->port + ($this->query_port_offset ?? 0);
        $host = $ip . ':' . $port;

        try {
            $gameQ = new GameQ();

            $gameQ->addServer([
                'type' => $this->query_type->value,
                'host' => $host,
            ]);

            $gameQ->setOption('debug', config('app.debug'));

            return $gameQ->process()[$host] ?? [];
        } catch (Exception $exception) {
            report($exception);
        }

        return [];
    }
}
