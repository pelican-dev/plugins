<?php

namespace Boy132\PlayerCounter\Enums;

use Filament\Support\Contracts\HasLabel;

enum GameQueryType: string implements HasLabel
{
    case Source = 'source';

    case MinecraftJava = 'minecraft';
    case MinecraftBedrock = 'minecraftbe';

    case Cs16 = 'cs16';
    case Rust = 'rust';
    case Arma3 = 'arma3';
    case ArkSe = 'arkse';
    case Squad = 'squad';
    case Unturned = 'unturned';
    case Valheim = 'valheim';
    case VRising = 'vrising';

    case Terraria = 'terraria';
    case Tshock = 'tshock';

    case SAMP = 'samp';
    case MultiTheftAuto = 'mta';
    case FiveMRedM = 'cfx';

    case Teamspeak3 = 'teamspeak3';
    case Mumble = 'mumble';

    public function getLabel(): string
    {
        if ($this === self::ArkSe) {
            return 'ARK: Survival Evolved';
        }

        if ($this === self::SAMP) {
            return 'SA:MP';
        }

        if ($this === self::FiveMRedM) {
            return 'FiveM / RedM';
        }

        if ($this === self::Cs16) {
            return 'Counter-Strike 1.6';
        }

        return str($this->name)->headline();
    }

    public function isMinecraft(): bool
    {
        return $this === self::MinecraftJava || $this === self::MinecraftBedrock;
    }
}
