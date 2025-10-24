<?php

namespace Boy132\RoboAvatars\Providers;

use App\Extensions\Avatar\AvatarService;
use Boy132\RoboAvatars\RoboAvatarsSchema;
use Illuminate\Support\ServiceProvider;

class RoboAvatarsPluginProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->app->make(AvatarService::class)->register(new RoboAvatarsSchema());
    }
}
