<?php

namespace Boy132\Subdomains\Models;

use Boy132\Subdomains\Services\CloudflareService;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property ?string $cloudflare_id
 * @property ?string $srv_target
 */
class CloudflareDomain extends Model
{
    protected $fillable = [
        'name',
        'cloudflare_id',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::created(function (self $model) {
            $service = new CloudflareService();

            $zoneId = $service->getZoneId($model->name);
            if (!$zoneId) {
                Notification::make()
                    ->title(trans('subdomains::strings.notifications.cloudflare_zone_fetch_failed', ['domain' => $model->name]))
                    ->danger()
                    ->send();
            } else {
                Notification::make()
                    ->title(trans('subdomains::strings.notifications.cloudflare_domain_saved', ['domain' => $model->name]))
                    ->success()
                    ->send();

                $model->update([
                    'cloudflare_id' => $zoneId,
                ]);
            }
        });
    }

    public function subdomains(): HasMany
    {
        return $this->hasMany(Subdomain::class, 'domain_id');
    }

    public function fetchCloudflareId(): void
    {
        // @phpstan-ignore staticMethod.notFound
        $response = Http::cloudflare()->get('zones', [
            'name' => $this->name,
        ])->json();

        if ($response['success']) {
            $zones = $response['result'];

            if (count($zones) > 0) {
                $this->update([
                    'cloudflare_id' => $zones[0]['id'],
                ]);
            }
        }
    }
}
