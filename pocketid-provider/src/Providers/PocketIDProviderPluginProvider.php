<?php

namespace Ebnater\PocketIDProvider\Providers;

use App\Extensions\OAuth\OAuthService;
use Ebnater\PocketIDProvider\Extensions\OAuth\Schemas\PocketIDSchema;
use Illuminate\Support\ServiceProvider;

class PocketIDProviderPluginProvider extends ServiceProvider
{
    public function boot(): void
    {
        $service = $this->app->make(OAuthService::class);
        $service->register(new PocketIDSchema());
    }
}
