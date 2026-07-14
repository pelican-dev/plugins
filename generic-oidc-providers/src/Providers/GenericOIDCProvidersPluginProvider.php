<?php

namespace Boy132\GenericOIDCProviders\Providers;

use App\Extensions\OAuth\OAuthService;
use App\Models\Role;
use Boy132\GenericOIDCProviders\Extensions\OAuth\Schemas\GenericOIDCProviderSchema;
use Boy132\GenericOIDCProviders\Models\GenericOIDCProvider;
use Illuminate\Support\ServiceProvider;

class GenericOIDCProvidersPluginProvider extends ServiceProvider
{
    public function boot(): void
    {
        Role::registerCustomDefaultPermissions('genericOidcProvider');
        Role::registerCustomModelIcon('genericOidcProvider', 'tabler-brand-oauth');

        $service = $this->app->make(OAuthService::class);

        $providers = GenericOIDCProvider::all();
        foreach ($providers as $provider) {
            $service->register(new GenericOIDCProviderSchema($provider));
        }
    }
}
