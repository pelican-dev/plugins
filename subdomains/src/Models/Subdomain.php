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
 * @property string $cloudflare_id
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

        static::creating(function (self $model) {
            // Relation does not exist yet, so we need to set it manually.
            if ($model->server_id) {
                $model->loadMissing('server.allocation');
            }

            $registrarUpdated = $model->upsertOnCloudflare();
            if (!$registrarUpdated) {
                return false;
            }

            Notification::make()
                ->success()
                ->title(trans('subdomains::strings.notifications.cloudflare_record_created_title'))
                ->body(trans('subdomains::strings.notifications.cloudflare_record_created', ['subdomain' => $model->getLabel(), 'record_type' => $model->record_type]))
                ->send();

            return true;
        });

        static::updating(function (self $model) {
            $registrarUpdated = $model->upsertOnCloudflare();
            if (!$registrarUpdated) {
                return false;
            }

            Notification::make()
                ->success()
                ->title(trans('subdomains::strings.notifications.cloudflare_record_updated_title'))
                ->body(trans('subdomains::strings.notifications.cloudflare_record_updated', ['subdomain' => $model->name . '.' . ($model->domain->name ?? 'unknown'), 'record_type' => $model->record_type]))
                ->send();

            return true;
        });

        static::deleting(function (self $model) {
            $registrarUpdated = $model->deleteOnCloudflare();
            if (!$registrarUpdated) {
                return false;
            }

            Notification::make()
                ->success()
                ->title(trans('subdomains::strings.notifications.cloudflare_record_deleted_title'))
                ->body(trans('subdomains::strings.notifications.cloudflare_record_deleted', ['subdomain' => $model->name . '.' . ($model->domain->name ?? 'unknown')]))
                ->send();

            return true;
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

    public function setSrvRecordAttribute(bool $isSrvRecord): void
    {
        if ($isSrvRecord) {
            $this->attributes['record_type'] = 'SRV';
        } else {
            $ip = $this->server?->allocation?->ip; // @phpstan-ignore nullsafe.neverNull
            if (!empty($ip) && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                $this->attributes['record_type'] = 'AAAA';
            } else {
                $this->attributes['record_type'] = 'A';
            }
        }
    }

    protected function upsertOnCloudflare(): bool
    {
        $registrar = app(CloudflareService::class);

        $zoneId = $this->domain?->cloudflare_id; // @phpstan-ignore nullsafe.neverNull
        $domainName = $this->domain?->name; // @phpstan-ignore nullsafe.neverNull

        if (empty($this->server) || empty($this->server->node)) {
            Log::warning('Subdomain server/node relation is null', ['subdomain_id' => $this->id]);
            Notification::make()
                ->danger()
                ->title(trans('subdomains::strings.notifications.cloudflare_upsert_failed_title'))
                ->body(trans('subdomains::strings.notifications.cloudflare_upsert_failed', ['subdomain' => $this->getLabel() ?? 'unknown', 'errors' => 'Server/node relation is null']))
                ->send();

            return false;
        }

        if (empty($this->server->allocation)) {
            Log::warning('Subdomain server allocation is null', ['subdomain_id' => $this->id, 'server_id' => $this->server->id]);
            Notification::make()
                ->danger()
                ->title(trans('subdomains::strings.notifications.cloudflare_upsert_failed_title'))
                ->body(trans('subdomains::strings.notifications.cloudflare_upsert_failed', ['subdomain' => $this->getLabel() ?? 'unknown', 'errors' => 'Server allocation is null']))
                ->send();

            return false;
        }

        if (empty($zoneId) || empty($domainName)) {
            Log::warning('Cloudflare zone id or name missing for domain', ['domain_id' => $this->domain_id]);
            Notification::make()
                ->danger()
                ->title(trans('subdomains::strings.notifications.cloudflare_missing_zone_title'))
                ->body(trans('subdomains::strings.notifications.cloudflare_missing_zone', ['domain' => $domainName, 'subdomain' => $this->getLabel() ?? 'unknown']))
                ->send();

            return false;
        }

        // SRV: target comes from node, port from server allocation
        if ($this->record_type === 'SRV') {
            $port = $this->server->allocation?->port; // @phpstan-ignore nullsafe.neverNull
            if (empty($port)) {
                Log::warning('Server missing allocation with port', $this->toArray());
                Notification::make()
                    ->danger()
                    ->title(trans('subdomains::strings.notifications.cloudflare_missing_srv_port_title'))
                    ->body(trans('subdomains::strings.notifications.cloudflare_missing_srv_port', ['server' => $this->server->name ?? 'unassigned']))
                    ->send();

                return false;
            }

            $serviceRecordType = ServiceRecordType::fromServer($this->server);
            if (!$serviceRecordType) {
                Log::warning('Unable to determine service record type for SRV record', ['server_id' => $this->server->id ?? 'unknown', 'server' => $this->server->name ?? 'unknown']);
                Notification::make()
                    ->danger()
                    ->title(trans('subdomains::strings.notifications.cloudflare_invalid_service_record_type_title'))
                    ->body(trans('subdomains::strings.notifications.cloudflare_invalid_service_record_type', ['subdomain' => $this->getLabel() ?? 'unknown']))
                    ->send();

                return false;
            }

            if (empty($this->server->node->srv_target)) {
                Log::warning('Node missing SRV target for SRV record', ['node_id' => $this->server->node->id]);
                Notification::make()
                    ->danger()
                    ->title(trans('subdomains::strings.notifications.cloudflare_missing_srv_target_title'))
                    ->body(trans('subdomains::strings.notifications.cloudflare_missing_srv_target', ['node' => $this->server->node->name ?? 'unknown']))
                    ->send();

                return false;
            }

            $result = $registrar->upsertDnsRecord($zoneId, $domainName, $this->name, 'SRV', $this->server->node->srv_target, $this->cloudflare_id, $port, $serviceRecordType);

            if ($result['success'] && !empty($result['id'])) {
                if ($this->cloudflare_id !== $result['id']) {
                    if ($this->exists) {
                        $this->updateQuietly(['cloudflare_id' => $result['id']]);
                    } else {
                        $this->cloudflare_id = $result['id'];
                    }
                }

                return true;
            }

            Notification::make()
                ->danger()
                ->title(trans('subdomains::strings.notifications.cloudflare_upsert_failed_title'))
                ->body(trans('subdomains::strings.notifications.cloudflare_upsert_failed', ['subdomain' => $this->getLabel() ?? 'unknown', 'errors' => json_encode($result['errors'])]))
                ->send();

            return false;
        }

        // A/AAAA
        $ip = $this->server->allocation?->ip; // @phpstan-ignore nullsafe.neverNull
        if (empty($ip) || $ip === '0.0.0.0' || $ip === '::') {
            Log::warning('Server allocation missing or invalid IP', ['server_id' => $this->server_id]);
            Notification::make()
                ->danger()
                ->title(trans('subdomains::strings.notifications.cloudflare_missing_ip_title'))
                ->body(trans('subdomains::strings.notifications.cloudflare_missing_ip', ['subdomain' => $this->getLabel() ?? 'unknown']))
                ->send();

            return false;
        }

        $result = $registrar->upsertDnsRecord($zoneId, $domainName, $this->name, $this->record_type, $ip, $this->cloudflare_id, null, null);

        if ($result['success'] && !empty($result['id'])) {
            if ($this->cloudflare_id !== $result['id']) {
                if ($this->exists) {
                    $this->updateQuietly(['cloudflare_id' => $result['id']]);
                } else {
                    $this->cloudflare_id = $result['id'];
                }
            }

            return true;
        }

        Notification::make()
            ->danger()
            ->title(trans('subdomains::strings.notifications.cloudflare_upsert_failed_title'))
            ->body(trans('subdomains::strings.notifications.cloudflare_upsert_failed', ['subdomain' => $this->getLabel() ?? 'unknown', 'errors' => json_encode($result['errors'])]))
            ->send();

        return false;
    }

    protected function deleteOnCloudflare(): bool
    {
        // No Cloudflare record to delete, consider it successful
        if (empty($this->cloudflare_id)) {
            Log::warning('Subdomain deleteOnCloudflare called but no cloudflare_id set', ['subdomain_id' => $this->id]);

            return true;
        }

        if (!$this->domain?->cloudflare_id) { // @phpstan-ignore nullsafe.neverNull
            Log::warning('Cloudflare zone missing for subdomain during subdomain delete', ['domain_id' => $this->domain_id]);
            Notification::make()
                ->danger()
                ->title(trans('subdomains::strings.notifications.cloudflare_delete_failed_title'))
                ->body(trans('subdomains::strings.notifications.cloudflare_delete_failed', ['errors' => 'Cloudflare zone missing for domain']))
                ->send();

            return false;
        }

        $registrar = app(CloudflareService::class);

        $result = $registrar->deleteDnsRecord($this->domain->cloudflare_id, $this->cloudflare_id);

        if (!empty($result['success']) || $result['status'] === 404) {
            return true;
        }

        Notification::make()
            ->danger()
            ->title(trans('subdomains::strings.notifications.cloudflare_delete_failed_title'))
            ->body(trans('subdomains::strings.notifications.cloudflare_delete_failed', ['subdomain' => $this->getLabel() ?? 'unknown', 'errors' => json_encode($result['errors'])]))
            ->send();

        return false;
    }
}
