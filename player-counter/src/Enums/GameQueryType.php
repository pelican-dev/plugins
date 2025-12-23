<?php

namespace Boy132\PlayerCounter\Enums;

use Filament\Support\Contracts\HasLabel;

enum GameQueryType: string implements HasLabel
{
    case Source = 'source';
    case MinecraftJava = 'minecraft';
    case MinecraftBedrock = 'minecraftbe';
    case Rust = 'rust';
    case Arma3 = 'arma3';
    case Terraria = 'terraria';
    case SAMP = 'samp';
    case MultiTheftAuto = 'mta';
    case FiveMRedM = 'cfx';
    case Teamspeak3 = 'teamspeak3';
    case Mumble = 'mumble';

    public function getLabel(): string
    {
        if ($this === self::SAMP) {
            return 'SA:MP';
        }

        if ($this === self::FiveMRedM) {
            return 'FiveM / RedM';
        }

        return str($this->name)->headline();
    }

    public function isMinecraft(): bool
    {
        return $this === self::MinecraftJava || $this === self::MinecraftBedrock;
    }
}
