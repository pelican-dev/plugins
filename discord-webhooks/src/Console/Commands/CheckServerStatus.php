<?php

namespace Notjami\Webhooks\Console\Commands;

use App\Models\Server;
use App\Repositories\Daemon\DaemonServerRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Notjami\Webhooks\Enums\WebhookEvent;
use Notjami\Webhooks\Models\Webhook;
use Notjami\Webhooks\Services\DiscordWebhookService;

class CheckServerStatus extends Command
{
    protected $signature = 'discord-webhooks:check-status';

    protected $description = 'Check server status and trigger webhooks for status changes';

    public function handle(DaemonServerRepository $repository, DiscordWebhookService $webhookService): int
    {
        // Get all servers that have webhooks configured
        $serverIds = Webhook::enabled()
            ->whereNotNull('server_id')
            ->pluck('server_id')
            ->unique();

        // Also check servers if there are global webhooks
        $hasGlobalWebhooks = Webhook::enabled()
            ->whereNull('server_id')
            ->exists();

        if ($hasGlobalWebhooks) {
            $servers = Server::whereNull('status')->get();
        } else {
            $servers = Server::whereIn('id', $serverIds)->whereNull('status')->get();
        }

        foreach ($servers as $server) {
            try {
                $details = $repository->setServer($server)->getDetails();
                $currentState = $details['state'] ?? 'offline';

                $cacheKey = "webhook_server_status_{$server->id}";
                $previousState = Cache::get($cacheKey, 'unknown');

                if ($previousState !== $currentState) {
                    Cache::put($cacheKey, $currentState, now()->addHours(24));

                    if ($previousState !== 'unknown') {
                        if ($currentState === 'running') {
                            $webhookService->triggerEvent(WebhookEvent::ServerStarted, $server);
                            $this->info("Server {$server->name} started - webhook triggered");
                        } elseif (in_array($currentState, ['offline', 'stopped'])) {
                            $webhookService->triggerEvent(WebhookEvent::ServerStopped, $server);
                            $this->info("Server {$server->name} stopped - webhook triggered");
                        }
                    }
                }
            } catch (\Exception $e) {
                $this->error("Failed to check server {$server->name}: {$e->getMessage()}");
            }
        }

        return self::SUCCESS;
    }
}
