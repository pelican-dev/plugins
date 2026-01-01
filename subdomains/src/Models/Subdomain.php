<?php

namespace Boy132\Subdomains\Models;

use App\Models\Server;
use Boy132\Subdomains\Enums\ServiceRecordType;
use Boy132\Subdomains\Services\CloudflareService;
use Filament\Notifications\Notification;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

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

    protected $appends = [
        'srv_record',
    ];

    protected static function boot(): void
    {
        parent::boot();

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

    public function setSrvRecordAttribute($isSrvRecord): void
    {
        if ($isSrvRecord) {
            $this->attributes['record_type'] = 'SRV';
        } else {
            $ip = $this->server?->allocation?->ip ?? null;
            if (!empty($ip) && filter_var($ip, FILTER_FLAG_IPV6)) {
                $this->attributes['record_type'] = 'AAAA';
            } else {
                $this->attributes['record_type'] = 'A';
            }
        }
    }

    protected function upsertOnCloudflare(): void
    {
        $registrar = app(CloudflareService::class);

        $zoneId = $this->domain->cloudflare_id;
        if (empty($zoneId)) {
            Log::warning('Cloudflare zone id missing for domain', ['domain_id' => $this->domain_id]);

            Notification::make()
                ->danger()
                ->title(trans('subdomains::strings.notifications.cloudflare_missing_zone_title'))
                ->body(trans('subdomains::strings.notifications.cloudflare_missing_zone', ['domain' => $this->domain->name ?? 'unknown', 'subdomain' => $this->name . '.' . ($this->domain->name ?? 'unknown')]))
                ->send();

            return;
        }

        // SRV: target comes from node, port from server allocation
        if ($this->record_type === 'SRV') {
            $port = $this->server && $this->server->allocation ? ($this->server->allocation->port ?? null) : null;

            if (empty($port)) {
                Log::warning('Server missing allocation with port', $this->toArray());
                Notification::make()
                    ->danger()
                    ->title(trans('subdomains::strings.notifications.cloudflare_missing_srv_port_title'))
                    ->body(trans('subdomains::strings.notifications.cloudflare_missing_srv_port', ['subdomain' => $this->name . '.' . ($this->domain->name ?? 'unknown')]))
                    ->send();

                return;
            }

            $serviceRecordType = ServiceRecordType::fromServer($this->server);
            if (!$serviceRecordType) {
                Log::warning('Unable to determine service record type for SRV record', ['server_id' => $this->server->id, 'server' => $this->server->name]);
                Notification::make()
                    ->danger()
                    ->title(trans('subdomains::strings.notifications.cloudflare_invalid_service_record_type_title'))
                    ->body(trans('subdomains::strings.notifications.cloudflare_invalid_service_record_type', ['subdomain' => $this->name . '.' . ($this->domain->name ?? 'unknown')]))
                    ->send();

                return;
            }

            if (!$this->server || !$this->server->node || empty($this->server->node->srv_target)) {
                Log::warning('Node missing SRV target for SRV record', ['node_id' => $this->server->node?->id ?? null]);
                Notification::make()
                    ->danger()
                    ->title(trans('subdomains::strings.notifications.cloudflare_missing_srv_target_title'))
                    ->body(trans('subdomains::strings.notifications.cloudflare_missing_srv_target', ['node' => $this->server->node->name]))
                    ->send();

                return;
            }

            $recordName = sprintf('%s.%s.%s', $serviceRecordType->service(), $serviceRecordType->protocol(), $this->name);

            $result = $registrar->upsertDnsRecord($zoneId, $recordName, 'SRV', $this->server->node->srv_target, $this->cloudflare_id, $port, $serviceRecordType);

            if ($result['success'] && !empty($result['id'])) {
                if ($this->cloudflare_id !== $result['id']) {
                    $this->updateQuietly(['cloudflare_id' => $result['id']]);
                }

                Notification::make()
                    ->success()
                    ->title(trans('subdomains::strings.notifications.cloudflare_record_updated_title'))
                    ->body(trans('subdomains::strings.notifications.cloudflare_record_updated', ['subdomain' => $this->name . '.' . ($this->domain->name ?? 'unknown'), 'record_type' => $this->record_type]))
                    ->send();
            } else {
                Log::error('Failed to upsert SRV record on Cloudflare for Subdomain ID ' . $this->id, ['result' => $result]);

                Notification::make()
                    ->danger()
                    ->title(trans('subdomains::strings.notifications.cloudflare_upsert_failed_title'))
                    ->body(trans('subdomains::strings.notifications.cloudflare_upsert_failed', ['subdomain' => $this->name . '.' . ($this->domain->name ?? 'unknown'), 'errors' => json_encode($result['errors'] ?? $result['body'] ?? [])]))
                    ->send();
            }

            return;
        }

        // A/AAAA
        if (!$this->server || !$this->server->allocation || $this->server->allocation->ip === '0.0.0.0' || $this->server->allocation->ip === '::') {
            Log::warning('Server allocation missing or invalid IP', ['server_id' => $this->server_id]);

            Notification::make()
                ->danger()
                ->title(trans('subdomains::strings.notifications.cloudflare_missing_ip_title'))
                ->body(trans('subdomains::strings.notifications.cloudflare_missing_ip', ['subdomain' => $this->name . '.' . ($this->domain->name ?? 'unknown')]))
                ->send();

            return;
        }

        $result = $registrar->upsertDnsRecord($zoneId, $this->name, $this->record_type, $this->server->allocation->ip, $this->cloudflare_id, null);

        if ($result['success'] && !empty($result['id'])) {
            if ($this->cloudflare_id !== $result['id']) {
                $this->updateQuietly(['cloudflare_id' => $result['id']]);
            }

            Notification::make()
                ->success()
                ->title(trans('subdomains::strings.notifications.cloudflare_record_updated_title'))
                ->body(trans('subdomains::strings.notifications.cloudflare_record_updated', ['subdomain' => $this->name . '.' . ($this->domain->name ?? 'unknown'), 'record_type' => $this->record_type]))
                ->send();
        } else {
            Log::error('Failed to upsert record on Cloudflare for Subdomain ID ' . $this->id, ['result' => $result]);

            $domainName = $this->domain->name ?? 'unknown';
            $sub = sprintf('%s.%s', $this->name, $domainName);

            Notification::make()
                ->danger()
                ->title(trans('subdomains::strings.notifications.cloudflare_upsert_failed_title'))
                ->body(trans('subdomains::strings.notifications.cloudflare_upsert_failed', ['subdomain' => $sub, 'errors' => json_encode($result['errors'] ?? $result['body'] ?? [])]))
                ->send();
        }
    }

    protected function deleteOnCloudflare(): void
    {
        if ($this->cloudflare_id && $this->domain && $this->domain->cloudflare_id) {
            $registrar = app(CloudflareService::class);

            $result = $registrar->deleteDnsRecord($this->domain->cloudflare_id, $this->cloudflare_id);

            if (!empty($result['success'])) {
                Notification::make()
                    ->success()
                    ->title(trans('subdomains::strings.notifications.cloudflare_delete_success_title'))
                    ->body(trans('subdomains::strings.notifications.cloudflare_delete_success', ['subdomain' => $this->name . '.' . ($this->domain->name ?? 'unknown')]))
                    ->send();
            } else {
                Notification::make()
                    ->danger()
                    ->title(trans('subdomains::strings.notifications.cloudflare_delete_failed_title'))
                    ->body(trans('subdomains::strings.notifications.cloudflare_delete_failed', ['subdomain' => $this->name . '.' . ($this->domain->name ?? 'unknown'), 'errors' => json_encode($result['errors'] ?? $result['body'] ?? [])]))
                    ->send();
            }

            return;
        }
    }
}
