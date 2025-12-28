<?php

namespace Boy132\Subdomains\Models;

use App\Models\Server;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Http;

/**
 * @property int $id
 * @property string $name
 * @property string $record_type
 * @property ?string $cloudflare_id
 * @property int $domain_id
 * @property CloudflareDomain $domain
 * @property int $server_id
 * @property Server $server
 */
class Subdomain extends Model implements HasLabel
{
    protected $fillable = [
        'name',
        'record_type',
        'cloudflare_id',
        'domain_id',
        'server_id',
        'srv_record',
    ];

    protected $casts = [
        'srv_record' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (self $model) {
            // If srv_record provided in the payload, ensure record_type follows it and then remove it
            if (array_key_exists('srv_record', $model->attributes)) {
                $srv = (bool) $model->attributes['srv_record'];

                if ($srv) {
                    $model->attributes['record_type'] = 'SRV';
                } else {
                    if ($model->server && $model->server->allocation && is_ipv6($model->server->allocation->ip)) {
                        $model->attributes['record_type'] = 'AAAA';
                    } else {
                        $model->attributes['record_type'] = 'A';
                    }
                }

                unset($model->attributes['srv_record']);
            }

            // If no record_type is present, set a sensible default based on server allocation
            if (!isset($model->attributes['record_type'])) {
                if ($model->server && $model->server->allocation && is_ipv6($model->server->allocation->ip)) {
                    $model->attributes['record_type'] = 'AAAA';
                } else {
                    $model->attributes['record_type'] = 'A';
                }
            }
        });

        static::created(function (self $model) {
            $model->createOnCloudflare();
        });

        static::updated(function (self $model) {
            $model->updateOnCloudflare();
        });

        static::deleted(function (self $model) {
            $model->deleteOnCloudflare();
        });
    }

    public function domain(): BelongsTo
    {
        return $this->belongsTo(CloudflareDomain::class, 'domain_id');
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function getLabel(): string|Htmlable|null
    {
        return $this->name . '.' . $this->domain->name;
    }

    public function getSrvRecordAttribute(): bool
    {
        return $this->record_type === 'SRV';
    }

    public function setSrvRecordAttribute($value): void
    {
        if ($value) {
            $this->attributes['record_type'] = 'SRV';
        } else {
            if ($this->server && $this->server->allocation && is_ipv6($this->server->allocation->ip)) {
                $this->attributes['record_type'] = 'AAAA';
            } else {
                $this->attributes['record_type'] = 'A';
            }
        }
    }

    protected function buildSrvPayload(): ?array
    {
        $target = $this->domain->srv_target ?? null;
        $port = $this->server && $this->server->allocation ? ($this->server->allocation->port ?? null) : null;

        if (empty($target) || empty($port)) {
            Log::error('SRV record target or port is missing for Subdomain ID ' . $this->id . '. Target: ' . ($target ?? 'null') . ', Port: ' . ($port ?? 'null'));
            return null;
        }

        $priority = (int) ($this->srv_priority ?? 0);
        $weight = (int) ($this->srv_weight ?? 0);
        $port = (int) $port;

        // Need to build the name to include the services and protocol parts for SRV records, this may vary based on game(egg tag)/server type
        // Temporrary placeholder for service and protocol = '_minecraft._tcp.'

        return [
            'name' => sprintf('_minecraft._tcp.%s', $this->name),
            'ttl' => 1,
            'type' => 'SRV',
            'comment' => 'Created by Pelican Subdomains plugin',
            'content' => sprintf('%d %d %d %s', $priority, $weight, $port, $target),
            'proxied' => false,
            'data' => [
                'priority' => $priority,
                'weight' => $weight,
                'port' => $port,
                'target' => $target,
            ],
        ];
    }

    protected function createOnCloudflare(): void
    {
        if ($this->record_type === 'SRV') {
            $payload = $this->buildSrvPayload();

            if ($payload === null) {
                return;
            }

            if (!$this->cloudflare_id) {
                $response = Http::cloudflare()->post("zones/{$this->domain->cloudflare_id}/dns_records", $payload)->json();

                if (!empty($response['success'])) {
                    $dnsRecord = $response['result'];

                    $this->updateQuietly([
                        'cloudflare_id' => $dnsRecord['id'],
                    ]);
                }
            }

            return;
        }

        if (!$this->server->allocation || $this->server->allocation->ip === '0.0.0.0' || $this->server->allocation->ip === '::') {
            return;
        }

        if (!$this->cloudflare_id) {
            $body = [
                'name' => $this->name,
                'ttl' => 1,
                'type' => $this->record_type,
                'comment' => 'Created by Pelican Subdomains plugin',
                'content' => $this->server->allocation->ip,
                'proxied' => false,
            ];

            $response = Http::cloudflare()->post("zones/{$this->domain->cloudflare_id}/dns_records", $body)->json();

            if ($response['success']) {
                $dnsRecord = $response['result'];

                $this->updateQuietly([
                    'cloudflare_id' => $dnsRecord['id'],
                ]);
            }
        }
    }

    protected function updateOnCloudflare(): void
    {
        if (!$this->server->allocation || $this->server->allocation->ip === '0.0.0.0' || $this->server->allocation->ip === '::') {
            return;
        }

        if ($this->record_type === 'SRV') {
            $payload = $this->buildSrvPayload();

            if ($payload === null) {
                return;
            }

            if ($this->cloudflare_id) {
                Http::cloudflare()->put("zones/{$this->domain->cloudflare_id}/dns_records/{$this->cloudflare_id}", $payload);
            } else {
                $response = Http::cloudflare()->post("zones/{$this->domain->cloudflare_id}/dns_records", $payload)->json();

                if (!empty($response['success'])) {
                    $dnsRecord = $response['result'];

                    $this->updateQuietly([
                        'cloudflare_id' => $dnsRecord['id'],
                    ]);
                }
            }

            return;
        } else {
            $body = [
                'name' => $this->name,
                'ttl' => 1,
                'type' => $this->record_type,
                'comment' => 'Created by Pelican Subdomains plugin',
                'content' => $this->server->allocation->ip,
                'proxied' => false,
            ];

            if ($this->cloudflare_id) {
                Http::cloudflare()->put("zones/{$this->domain->cloudflare_id}/dns_records/{$this->cloudflare_id}", $body);
            } else {
                $response = Http::cloudflare()->post("zones/{$this->domain->cloudflare_id}/dns_records", $body)->json();

                if (!empty($response['success'])) {
                    $dnsRecord = $response['result'];

                    $this->updateQuietly([
                        'cloudflare_id' => $dnsRecord['id'],
                    ]);
                }
            }
        }

    }

    protected function deleteOnCloudflare(): void
    {
        if ($this->cloudflare_id) {
            Http::cloudflare()->delete("zones/{$this->domain->cloudflare_id}/dns_records/{$this->cloudflare_id}");
        }
    }
}
