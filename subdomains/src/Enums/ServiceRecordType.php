<?php

namespace Boy132\Subdomains\Enums;

use App\Models\Server;
use Filament\Support\Contracts\HasLabel;

enum ServiceRecordType: string implements HasLabel
{
    // Service record types
    case minecraft = '_minecraft._tcp';

    public function getLabel(): string
    {
        return str($this->name)->title();
    }

    public static function isSupported(Server $server): bool
    {
        $tags = $server->egg->tags ?? [];
        foreach (self::cases() as $type) {
            if (in_array($type->name, $tags)) {
                return true;
            }
        }

        return false;
    }

    public static function fromServer(Server $server): ?self
    {
        $tags = $server->egg->tags ?? [];

        return self::fromTags($tags);
    }

    /** @param string[] $tags */
    public static function fromTags(array $tags): ?self
    {
        foreach (self::cases() as $type) {
            if (in_array($type->name, $tags)) {
                return $type;
            }
        }

        return null;
    }

    public function service(): string
    {
        $parts = explode('.', $this->value);

        return $parts[0];
    }

    public function protocol(): string
    {
        $parts = explode('.', $this->value);

        return $parts[1];
    }
}
