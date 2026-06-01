<?php

namespace Boy132\PlayerCounter\Models;

use App\Models\Allocation;
use App\Models\Egg;
use App\Models\Server;
use Boy132\PlayerCounter\Extensions\Query\QueryTypeService;
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

        $host = self::getHost($server->allocation);
        if ($host === false) {
            return null;
        }

        $port = $server->allocation->port + ($this->query_port_offset ?? 0);

        if ($this->query_port_variable) {
            $variableValue = $server->variables()->where('env_variable', $this->query_port_variable)->first()?->server_value;

            if ($variableValue && is_numeric($variableValue)) {
                $port = (int) $variableValue;
            }
        }

        /** @var QueryTypeService $service */
        $service = app(QueryTypeService::class);

        return $service->get($this->query_type)?->process($server, $host, $port);
    }

    public static function canRunQuery(?Allocation $allocation): bool
    {
        return self::getHost($allocation) !== false;
    }

    private static function isValidHost(string $address): bool
    {
        return self::normaliseIpAddress($address) !== false ||
            filter_var($address, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) !== false;
    }

    private static function normaliseIpAddress(string $address): bool|string
    {
        $address = inet_pton($address);
        if ($address === false) {
            return false;
        }

        $address = inet_ntop($address);
        if ($address === false) {
            return false;
        }

        if (filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            if ($address === '::') {
                return false;
            }

            return '[' . $address . ']';
        } elseif (filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            if ($address === '0.0.0.0') {
                return false;
            }

            return $address;
        }

        return false;
    }

    protected static function getHost(?Allocation $allocation): bool|string
    {
        if (!$allocation) {
            return false;
        }

        $address = false;

        if (config('player-counter.use_alias') && $allocation->alias && self::isValidHost($allocation->alias)) {
            $address = $allocation->alias;
        } elseif (self::isValidHost($allocation->ip)) {
            $address = $allocation->ip;
        } else {
            return false;
        }

        if (($ip = self::normaliseIpAddress($address)) !== false) {
            $address = $ip;
        }

        return $address;
    }
}
