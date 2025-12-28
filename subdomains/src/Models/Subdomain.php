<?php

namespace Boy132\Subdomains\Models;

use App\Models\Server;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;
use Boy132\Subdomains\Services\CloudflareService;

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

        static::saved(function (self $model) {
            $model->upsertOnCloudflare();
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



    protected function upsertOnCloudflare(): void
    {
        $service = app(CloudflareService::class);

        $zoneId = $this->domain->cloudflare_id;
        if (empty($zoneId)) {
            Log::warning('Cloudflare zone id missing for domain', ['domain_id' => $this->domain_id]);

            Notification::make()
                ->danger()
                ->title('Cloudflare: Missing Zone ID')
                ->body(sprintf('Cloudflare zone ID is not configured for %s. Cannot upsert DNS record for %s.%s.', $this->domain->name ?? 'unknown', $this->name, $this->domain->name ?? 'unknown'))
                ->send();

            return;
        }

        // SRV: target comes from domain, port from server allocation
        if ($this->record_type === 'SRV') {
            $port = $this->server && $this->server->allocation ? ($this->server->allocation->port ?? null) : null;

            if (empty($port)) {
                Log::warning('Server missing allocation with port', ['server_id' => $this->server_id]);

                Notification::make()
                    ->danger()
                    ->title('Cloudflare: Missing SRV Port')
                    ->body(sprintf('SRV target or port is missing for %s.%s. Cannot upsert SRV record.', $this->name, $this->domain->name ?? 'unknown'))
                    ->send();

                return;
            }

            $result = $service->upsertDnsRecord($zoneId, $this->name, 'SRV', $this->domain->srv_target, $port);

            if ($result['success'] && !empty($result['id'])) {
                if ($this->cloudflare_id !== $result['id']) {
                    $this->updateQuietly(['cloudflare_id' => $result['id']]);
                }
            } else {
                Log::error('Failed to upsert SRV record on Cloudflare for Subdomain ID ' . $this->id, ['result' => $result]);

                Notification::make()
                    ->danger()
                    ->title('Cloudflare: SRV upsert failed')
                    ->body('Failed to upsert SRV record for ' . $this->name . '.' . ($this->domain->name ?? 'unknown') . '. See logs for details. Errors: ' . json_encode($result['errors'] ?? $result['body'] ?? []))
                    ->send();
            }

            return;
        }

        // A/AAAA
        if (!$this->server->allocation || $this->server->allocation->ip === '0.0.0.0' || $this->server->allocation->ip === '::') {
            return;
        }

        $result = $service->upsertDnsRecord($zoneId, $this->name, $this->record_type, $ip, null, null, $this->domain->name);

        if ($result['success'] && !empty($result['id'])) {
            if ($this->cloudflare_id !== $result['id']) {
                $this->updateQuietly(['cloudflare_id' => $result['id']]);
            }
        } else {
            Log::error('Failed to upsert record on Cloudflare for Subdomain ID ' . $this->id, ['result' => $result]);

            $domainName = $this->domain->name ?? 'unknown';
            $sub = sprintf('%s.%s', $this->name, $domainName);

            Notification::make()
                ->danger()
                ->title('Cloudflare: Upsert failed')
                ->body('Failed to upsert record for ' . $sub . '. See logs for details. Errors: ' . json_encode($result['errors'] ?? $result['body'] ?? []))
                ->send();
        }
    }



    protected function deleteOnCloudflare(): void
    {
        if ($this->cloudflare_id && $this->domain && $this->domain->cloudflare_id) {
            $service = app(CloudflareService::class);

            $result = $service->deleteDnsRecord($this->domain->cloudflare_id, $this->cloudflare_id);

            if (!empty($result['success'])) {
                Log::info('Deleted Cloudflare record for Subdomain ID ' . $this->id, ['result' => $result]);
            } else {
                Log::warning('Failed to delete Cloudflare record for Subdomain ID ' . $this->id, ['result' => $result]);

                Notification::make()
                    ->danger()
                    ->title('Cloudflare: Delete failed')
                    ->body('Failed to delete Cloudflare record for ' . $this->name . '.' . ($this->domain->name ?? 'unknown') . '. See logs for details. Errors: ' . json_encode($result['errors'] ?? $result['body'] ?? []))
                    ->send();

            Notification::make()
                ->danger()
                ->title('Cloudflare: Upsert failed')
                ->body('Failed to upsert record for ' . $this->name . '.' . ($this->domain->name ?? 'unknown') . '. See logs for details. Errors: ' . json_encode($result['errors'] ?? $result['body'] ?? []))
                ->send();
            }
        }
    }
}
