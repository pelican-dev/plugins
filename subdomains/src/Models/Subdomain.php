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
    ];

    protected static function boot(): void
    {
        parent::boot();

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

    protected function createOnCloudflare(): void
    {
        if (!$this->server->allocation || $this->server->allocation->ip === '0.0.0.0' || $this->server->allocation->ip === '::') {
            return;
        }

        if (!$this->cloudflare_id) {
            // @phpstan-ignore staticMethod.notFound
            $response = Http::cloudflare()->post("zones/{$this->domain->cloudflare_id}/dns_records", [
                'name' => $this->name,
                'ttl' => 120,
                'type' => $this->record_type,
                'comment' => 'Created by Pelican Subdomains plugin',
                'content' => $this->server->allocation->ip,
                'proxied' => false,
            ])->json();

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

        if ($this->cloudflare_id) {
            // @phpstan-ignore staticMethod.notFound
            Http::cloudflare()->patch("zones/{$this->domain->cloudflare_id}/dns_records/{$this->cloudflare_id}", [
                'name' => $this->name,
                'ttl' => 120,
                'type' => $this->record_type,
                'comment' => 'Created by Pelican Subdomains plugin',
                'content' => $this->server->allocation->ip,
                'proxied' => false,
            ]);
        }
    }

    protected function deleteOnCloudflare(): void
    {
        if ($this->cloudflare_id) {
            // @phpstan-ignore staticMethod.notFound
            Http::cloudflare()->delete("zones/{$this->domain->cloudflare_id}/dns_records/{$this->cloudflare_id}");
        }
    }
}
