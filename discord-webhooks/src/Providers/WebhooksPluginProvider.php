<?php

namespace Notjami\Webhooks\Providers;

use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\Server;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Notjami\Webhooks\Console\Commands\CheckServerStatus;
use Notjami\Webhooks\Enums\WebhookEvent;
use Notjami\Webhooks\Services\DiscordWebhookService;

class WebhooksPluginProvider extends ServiceProvider
{
    public function register(): void
    {
        Role::registerCustomDefaultPermissions('discord-webhook');
        Role::registerCustomModelIcon('discord-webhook', 'tabler-webhook');

        $this->app->singleton(DiscordWebhookService::class, function () {
            return new DiscordWebhookService();
        });

        // Register console commands
        $this->commands([
            CheckServerStatus::class,
        ]);
    }

    public function boot(): void
    {
        $this->registerServerStatusListeners();
        $this->registerScheduledTasks();
    }

    protected function registerServerStatusListeners(): void
    {
        // Listen for Pelican Panel server events
        // These events are dispatched when server status changes

        // Server Installation Events
        Event::listen('eloquent.updating: App\Models\Server', function (Server $server) {
            // Check if status changed to installing
            if (
                $server->isDirty('status') &&
                ((is_string($server->status) && $server->status === 'installing') ||
                (method_exists($server->status, 'value') && $server->status->value === 'installing'))
            ) {
                DB::afterCommit(function () use ($server) {
                    app(DiscordWebhookService::class)->triggerEvent(WebhookEvent::ServerInstalling, $server);
                });
            }

            // Check if installation completed (status changed from installing to null/running)
            $original = $server->getOriginal('status');
            $isOriginalInstalling = (is_string($original) && $original === 'installing') || (method_exists($original, 'value') && $original->value === 'installing');
            if ($server->isDirty('status') && $isOriginalInstalling && $server->status === null) {
                DB::afterCommit(function () use ($server) {
                    app(DiscordWebhookService::class)->triggerEvent(WebhookEvent::ServerInstalled, $server);
                });
            }
        });

        // Try to listen for daemon status events if they exist
        $statusEvents = [
            'App\Events\Server\Started' => WebhookEvent::ServerStarted,
            'App\Events\Server\Stopped' => WebhookEvent::ServerStopped,
        ];

        foreach ($statusEvents as $eventClass => $webhookEvent) {
            if (class_exists($eventClass)) {
                Event::listen($eventClass, function ($event) use ($webhookEvent) {
                    $server = $event->server ?? null;
                    if ($server instanceof Server) {
                        // Only update the cache/state, do not send webhook here
                        $cacheKey = "webhook_server_status_{$server->id}";
                        $state = $webhookEvent === WebhookEvent::ServerStarted ? 'running' : 'offline';
                        \Illuminate\Support\Facades\Cache::put($cacheKey, $state, now()->addHours(24));
                    }
                });
            }
        }
    }

    protected function registerScheduledTasks(): void
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('discord-webhooks:check-status')->everyMinute();
        });
    }
}
