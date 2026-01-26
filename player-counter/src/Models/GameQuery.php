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
        $ip = config('player-counter.use_alias') && is_ip($allocation->alias) ? $allocation->alias : $allocation->ip;
        $ip = is_ipv6($ip) ? '[' . $ip . ']' : $ip;

        $host = $ip . ':' . $allocation->port;

        try {
            $data = [
                'type' => $this->query_type->value,
                'host' => $host,
            ];

            if ($this->query_port_offset) {
                $data['options'] = [
                    'query_port' => $allocation->port + $this->query_port_offset,
                ];
            }

            $gameQ = new GameQ();

            $gameQ->addServer($data);

            $gameQ->setOption('debug', config('app.debug'));

            return $gameQ->process()[$host] ?? [];
        } catch (Exception $exception) {
            report($exception);
        }

        return [];
    }
}
