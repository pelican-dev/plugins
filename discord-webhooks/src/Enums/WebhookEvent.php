<?php

namespace Notjami\Webhooks\Enums;

enum WebhookEvent: string
{
    case ServerStarted = 'server_started';
    case ServerStopped = 'server_stopped';
    case ServerInstalling = 'server_installing';
    case ServerInstalled = 'server_installed';

    public function getLabel(): string
    {
        return match ($this) {
            self::ServerStarted => 'Server Started',
            self::ServerStopped => 'Server Stopped',
            self::ServerInstalling => 'Server Installing',
            self::ServerInstalled => 'Server Installed',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::ServerStarted => 'Triggered when a server comes online',
            self::ServerStopped => 'Triggered when a server goes offline',
            self::ServerInstalling => 'Triggered when a server starts installing',
            self::ServerInstalled => 'Triggered when a server finishes installation',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::ServerStarted => '3066993',    // Green
            self::ServerStopped => '15158332',   // Red
            self::ServerInstalling => '15105570', // Orange
            self::ServerInstalled => '3447003',   // Blue
        };
    }

    public function getEmoji(): string
    {
        return match ($this) {
            self::ServerStarted => '🟢',
            self::ServerStopped => '🔴',
            self::ServerInstalling => '🔧',
            self::ServerInstalled => '✅',
        };
    }
}
