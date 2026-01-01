<?php

namespace Boy132\Subdomains\Providers;

use App\Filament\Admin\Resources\Servers\ServerResource;
use App\Models\Role;
use App\Models\Server;
use Boy132\Subdomains\Filament\Admin\Resources\Users\RelationManagers\SubdomainRelationManager;
use Boy132\Subdomains\Models\Subdomain;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class SubdomainsPluginProvider extends ServiceProvider
{
    public function register(): void
    {
        ServerResource::registerCustomRelations(SubdomainRelationManager::class);

        Role::registerCustomDefaultPermissions('cloudflare_domain');
        Role::registerCustomModelIcon('cloudflare_domain', 'tabler-world-www');
    }

    public function boot(): void
    {
        Http::macro(
            'cloudflare',
            fn () => Http::acceptJson()
                ->withToken(config('subdomains.token'))
                ->timeout(5)
                ->connectTimeout(1)
                ->baseUrl('https://api.cloudflare.com/client/v4/')
                ->throw()
        );

        Server::resolveRelationUsing('subdomains', fn (Server $server) => $server->hasMany(Subdomain::class, 'server_id', 'id'));
    }
}
