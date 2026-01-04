<?php

namespace Ebnater\PocketIDProvider\Providers;

use App\Extensions\OAuth\OAuthService;
use Ebnater\PocketIDProvider\Extensions\OAuth\Schemas\PocketIDSchema;
use Illuminate\Support\ServiceProvider;

class PocketIDProviderPluginProvider extends ServiceProvider
{
    /**
     * Register the PocketID OAuth schema with the application's OAuthService.
     *
     * Resolves the OAuthService from the service container and registers a new PocketIDSchema instance.
     */
    public function boot(): void
    {
        $service = $this->app->make(OAuthService::class);
        $service->register(new PocketIDSchema());
    }
}