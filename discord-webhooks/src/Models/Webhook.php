<?php

namespace Notjami\Webhooks\Models;

use App\Models\Server;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Notjami\Webhooks\Enums\WebhookEvent;

/**
 * @property int $id
 * @property string $name
 * @property string $webhook_url
 * @property int|null $server_id
 * @property array<WebhookEvent> $events
 * @property bool $enabled
 * @property Carbon|null $last_triggered_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Server|null $server
 */
class Webhook extends Model
{
    protected $fillable = [
        'name',
        'webhook_url',
        'server_id',
        'events',
        'enabled',
        'last_triggered_at',
    ];

    protected $attributes = [
        'events' => '[]',
        'enabled' => true,
    ];

    protected function casts(): array
    {
        return [
            'events' => 'array',
            'enabled' => 'boolean',
            'last_triggered_at' => 'datetime',
        ];
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function hasEvent(WebhookEvent $event): bool
    {
        return in_array($event->value, $this->events);
    }

    public function appliesToServer(Server $server): bool
    {
        return $this->server_id === null || $this->server_id === $server->id;
    }

    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }

    public function scopeForEvent($query, WebhookEvent $event)
    {
        return $query->whereJsonContains('events', $event->value);
    }

    public function scopeForServer($query, Server $server)
    {
        return $query->where(function ($q) use ($server) {
            $q->whereNull('server_id')
              ->orWhere('server_id', $server->id);
        });
    }
}
