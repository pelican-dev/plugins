<?php

namespace Boy132\Subdomains\Enums;

use App\Models\Server;
use Filament\Support\Contracts\HasLabel;

enum SRVServiceType: string implements HasLabel
{
    case Minecraft = '_minecraft._tcp';
    case Mumble = '_mumble._tcp';
    case Factorio = '_factorio._udp';
    case Rust = '_rust._udp';
    case SCPSL = '_scpsl._udp';
    case Teamspeak = '_ts3._udp';

    public function getLabel(): string
    {
        return str($this->name)->title();
    }

    public static function fromServer(Server $server): ?self
    {
        $features = $server->egg->features ?? [];

        foreach (self::cases() as $type) {
            $name = str($type->name)->lower()->prepend('srv-');

            if (in_array($name, $features)) {
                return $type;
            }
        }

        return null;
    }
}
