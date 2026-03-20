<?php

namespace Notjami\Webhooks\Services;

use App\Models\Server;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Notjami\Webhooks\Enums\WebhookEvent;
use Notjami\Webhooks\Models\Webhook;

class DiscordWebhookService
{
    public function sendTestMessage(Webhook $webhook): bool
    {
        $payload = [
            'embeds' => [
                [
                    'title' => '🔔 Webhook Test',
                    'description' => 'This is a test message from Pelican Panel.',
                    'color' => 3447003,
                    'fields' => [
                        [
                            'name' => 'Webhook Name',
                            'value' => $webhook->name,
                            'inline' => true,
                        ],
                        [
                            'name' => 'Status',
                            'value' => '✅ Working',
                            'inline' => true,
                        ],
                    ],
                    'footer' => [
                        'text' => 'Pelican Panel Webhooks',
                    ],
                    'timestamp' => now()->toIso8601String(),
                ],
            ],
        ];

        return $this->send($webhook, $payload);
    }

    public function sendServerEvent(Webhook $webhook, Server $server, WebhookEvent $event): bool
    {
        $payload = [
            'embeds' => [
                [
                    'title' => $event->getEmoji() . ' ' . $event->getLabel(),
                    'description' => $this->getEventDescription($event, $server),
                    'color' => (int) $event->getColor(),
                    'fields' => [
                        [
                            'name' => 'Server',
                            'value' => $server->name,
                            'inline' => true,
                        ],
                        [
                            'name' => 'UUID',
                            'value' => '`' . substr($server->uuid, 0, 8) . '...`',
                            'inline' => true,
                        ],
                        [
                            'name' => 'Owner',
                            'value' => $server->user->username ?? 'Unknown',
                            'inline' => true,
                        ],
                        [
                            'name' => 'Node',
                            'value' => $server->node->name ?? 'Unknown',
                            'inline' => true,
                        ],
                    ],
                    'footer' => [
                        'text' => 'Pelican Panel Webhooks',
                    ],
                    'timestamp' => now()->toIso8601String(),
                ],
            ],
        ];

        return $this->send($webhook, $payload);
    }

    public function triggerEvent(WebhookEvent $event, Server $server): void
    {
        $webhooks = Webhook::enabled()
            ->forEvent($event)
            ->forServer($server)
            ->get();

        foreach ($webhooks as $webhook) {
            try {
                $sent = $this->sendServerEvent($webhook, $server, $event);
                if ($sent) {
                    $webhook->update(['last_triggered_at' => now()]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to send webhook', [
                    'webhook_id' => $webhook->id,
                    'event' => $event->value,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    protected function send(Webhook $webhook, array $payload): bool
    {
        // Enforce Discord webhook URL pattern as a second layer of validation
        if (!preg_match('/^https:\/\/discord\.com\/api\/webhooks\/.+/', $webhook->webhook_url)) {
            Log::warning('Rejected non-Discord webhook URL', [
                'webhook_id' => $webhook->id,
                'url' => $webhook->webhook_url,
            ]);
            return false;
        }
        try {
            $response = Http::timeout(10)
                ->post($webhook->webhook_url, $payload);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Discord webhook failed', [
                'webhook_id' => $webhook->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    protected function getEventDescription(WebhookEvent $event, Server $server): string
    {
        return match ($event) {
            WebhookEvent::ServerStarted => "Server **{$server->name}** is now online.",
            WebhookEvent::ServerStopped => "Server **{$server->name}** has gone offline.",
            WebhookEvent::ServerInstalling => "Server **{$server->name}** is being installed.",
            WebhookEvent::ServerInstalled => "Server **{$server->name}** has been installed successfully.",
        };
    }
}
