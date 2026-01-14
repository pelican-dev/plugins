<?php

namespace ServerStatus\PlayerCounter\Enums;

use Filament\Support\Contracts\HasLabel;

enum GameQueryType: string implements HasLabel
{
    // Source Engine Games
    case Source = 'source';
    case CSGO = 'csgo';
    case CSS = 'css';
    case CS16 = 'cs16';
    case CS15 = 'cs15';
    case CSCZ = 'cscz';
    case TF2 = 'tf2';
    case L4D = 'l4d';
    case L4D2 = 'l4d2';
    case Gmod = 'gmod';
    case HL1 = 'hl1';
    case HL2DM = 'hl2dm';
    case DOD = 'dod';
    case DODS = 'dods';
    case BlackMesa = 'blackmesa';
    case Insurgency = 'insurgency';
    case InsurgencySandstorm = 'insurgencysand';
    case KillingFloor = 'killingfloor';
    case KillingFloor2 = 'killingfloor2';
    case NMRIH = 'nmrih';
    case NS2 = 'ns2';
    case Contagion = 'contagion';
    case FistfulOfFrags = 'fof';
    case TheShip = 'ship';
    case Brink = 'brink';

    // Minecraft
    case MinecraftJava = 'minecraft';
    case MinecraftBedrock = 'minecraftbe';
    case MinecraftPE = 'minecraftpe';

    // Survival Games
    case Rust = 'rust';
    case ArkSe = 'arkse';
    case ArkSa = 'arksa';
    case Valheim = 'valheim';
    case VRising = 'vrising';
    case SevenDaysToDie = 'sevendaystodie';
    case ConanExiles = 'conanexiles';
    case TheForest = 'theforrest';
    case DayZ = 'dayz';
    case DayZMod = 'dayzmod';
    case Miscreated = 'miscreated';
    case Hurtworld = 'hurtworld';
    case Unturned = 'unturned';
    case LifeIsFeudal = 'lifeisfeudal';
    case Eco = 'eco';
    case Barotrauma = 'barotrauma';
    case Avorion = 'avorion';
    case Stationeers = 'stationeers';
    case Stormworks = 'stormworks';
    case Wurm = 'wurm';

    // Military Simulators
    case Arma3 = 'arma3';
    case Arma = 'arma';
    case Arma2OA = 'armedassault2oa';
    case ArmedAssault3 = 'armedassault3';
    case Squad = 'squad';
    case PostScriptum = 'postscriptum';
    case HellLetLoose = 'hll';
    case Battalion1944 = 'batt1944';
    case ProjectReality = 'projectrealitybf2';
    case AmericasArmy3 = 'aa3';
    case AmericasArmyPG = 'aapg';

    // Battlefield Series
    case BF2 = 'bf2';
    case BF3 = 'bf3';
    case BF4 = 'bf4';
    case BF1942 = 'bf1942';
    case BFBC2 = 'bfbc2';
    case BFH = 'bfh';

    // Call of Duty Series
    case COD = 'cod';
    case COD2 = 'cod2';
    case COD4 = 'cod4';
    case CODMW2 = 'codmw2';
    case CODMW3 = 'codmw3';
    case CODUO = 'coduo';
    case CODWAW = 'codwaw';

    // Space Games
    case SpaceEngineers = 'spaceengineers';
    case Starmade = 'starmade';
    case Atlas = 'atlas';

    // Sandbox/Building
    case Terraria = 'terraria';
    case Tshock = 'tshock';

    // GTA/Racing
    case SAMP = 'samp';
    case MultiTheftAuto = 'mta';
    case FiveMRedM = 'cfx';
    case GTA5M = 'gta5m';
    case JustCause2 = 'justcause2';
    case JustCause3 = 'justcause3';
    case RFactor = 'rfactor';
    case RFactor2 = 'rf2';

    // MMO/RPG
    case Citadel = 'citadel';
    case Modiverse = 'modiverse';

    // Classic FPS
    case Quake2 = 'quake2';
    case Quake3 = 'quake3';
    case Quake4 = 'quake4';
    case QuakeLive = 'quakelive';
    case EnemyTerritory = 'et';
    case ETQW = 'etqw';
    case Doom3 = 'doom3';
    case Crysis = 'crysis';
    case Crysis2 = 'crysis2';
    case CrysisWars = 'crysiswars';
    case Halo = 'halo';
    case Kingpin = 'kingpin';
    case MOHAA = 'mohaa';
    case SOF2 = 'sof2';
    case SeriousSam = 'serioussam';
    case SWAT4 = 'swat4';
    case UrbanTerror = 'urbanterror';
    case Warsow = 'warsow';

    // Unreal Tournament
    case UT = 'ut';
    case UT2004 = 'ut2004';
    case UT3 = 'ut3';
    case Unreal2 = 'unreal2';

    // Star Wars
    case JediAcademy = 'jediacademy';
    case JediOutcast = 'jedioutcast';

    // Strategy
    case DawnOfWar = 'dow';

    // Other Popular Games
    case RedOrchestra2 = 'redorchestra2';
    case RedOrchestraOstfront = 'redorchestraostfront';
    case RisingStorm2 = 'risingstorm2';
    case Mordhau = 'mordhau';
    case Pixark = 'pixark';
    case ProjectZomboid = 'zomboid';
    case Soldat = 'soldat';
    case Teeworlds = 'teeworlds';
    case OpenTTD = 'openttd';
    case Tibia = 'tibia';
    case CS2D = 'cs2d';

    // Voice Servers
    case Teamspeak3 = 'teamspeak3';
    case Teamspeak2 = 'teamspeak2';
    case Mumble = 'mumble';
    case Ventrilo = 'ventrilo';

    public function getLabel(): string
    {
        return match($this) {
            self::ArkSe => 'ARK: Survival Evolved',
            self::ArkSa => 'ARK: Survival Ascended',
            self::SAMP => 'SA:MP',
            self::FiveMRedM => 'FiveM / RedM',
            self::CSGO => 'CS:GO',
            self::CSS => 'CS:Source',
            self::CS16 => 'CS 1.6',
            self::TF2 => 'Team Fortress 2',
            self::L4D => 'Left 4 Dead',
            self::L4D2 => 'Left 4 Dead 2',
            self::Gmod => "Garry's Mod",
            self::HL2DM => 'Half-Life 2: Deathmatch',
            self::DOD => 'Day of Defeat',
            self::DODS => 'Day of Defeat: Source',
            self::NMRIH => 'No More Room in Hell',
            self::BF2 => 'Battlefield 2',
            self::BF3 => 'Battlefield 3',
            self::BF4 => 'Battlefield 4',
            self::BF1942 => 'Battlefield 1942',
            self::BFBC2 => 'Battlefield: Bad Company 2',
            self::BFH => 'Battlefield Hardline',
            self::COD => 'Call of Duty',
            self::COD2 => 'Call of Duty 2',
            self::COD4 => 'Call of Duty 4',
            self::CODMW2 => 'Call of Duty: Modern Warfare 2',
            self::CODMW3 => 'Call of Duty: Modern Warfare 3',
            self::CODUO => 'Call of Duty: United Offensive',
            self::CODWAW => 'Call of Duty: World at War',
            self::UT => 'Unreal Tournament',
            self::UT2004 => 'Unreal Tournament 2004',
            self::UT3 => 'Unreal Tournament 3',
            self::MinecraftJava => 'Minecraft: Java Edition',
            self::MinecraftBedrock => 'Minecraft: Bedrock Edition',
            self::SevenDaysToDie => '7 Days to Die',
            self::HellLetLoose => 'Hell Let Loose',
            self::ProjectZomboid => 'Project Zomboid',
            default => str($this->name)->headline(),
        };
    }

    public function isMinecraft(): bool
    {
        return $this === self::MinecraftJava || $this === self::MinecraftBedrock;
    }

    /**
     * Get the default query port offset for this game type.
     * Based on the $port_diff property in GameQ protocol files.
     * Returns null if no offset is needed (port = server port).
     */
    public function getDefaultQueryPortOffset(): ?int
    {
        return match($this) {
            // Offset +1
            self::Arma3,
            self::Arma2OA,
            self::Barotrauma,
            self::Brink,
            self::Eco,
            self::JustCause3,
            self::KillingFloor,
            self::NS2,
            self::Pixark,
            self::Rust,
            self::SeriousSam,
            self::Stormworks,
            self::SWAT4,
            self::TheForest,
            self::UT,
            self::Unturned,
            self::Valheim,
            self::VRising => 1,
            
            // Offset +2
            self::CODMW3,
            self::LifeIsFeudal,
            self::Miscreated,
            self::RFactor2 => 2,
            
            // Offset +3
            self::Battalion1944 => 3,
            
            // Offset +10
            self::PostScriptum => 10,
            
            // Offset +15
            self::HellLetLoose => 15,
            
            // Offset +29
            self::InsurgencySandstorm => 29,
            
            // Offset +101
            self::Terraria => 101,
            
            // Offset +123
            self::MultiTheftAuto,
            self::Soldat => 123,
            
            // Offset +8433
            self::BF1942 => 8433,
            
            // Offset +13333
            self::BF2 => 13333,
            
            // Offset +19238
            self::ArkSe,
            self::KillingFloor2,
            self::RedOrchestra2 => 19238,
            
            // Offset +19378
            self::Squad => 19378,
            
            // Offset +22000
            self::BF3 => 22000,
            
            // Offset +29321
            self::BFBC2 => 29321,
            
            // Offset +51800
            self::Atlas => 51800,
            
            // Offset +18243
            self::AmericasArmy3 => 18243,
            
            // Negative offset -36938
            self::Mumble => -36938,
            
            // All other games use the same port for server and queries
            default => null,
        };
    }
}
