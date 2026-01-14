<?php

namespace ServerStatus\PlayerCounter\Database\Seeders;

use App\Models\Egg;
use ServerStatus\PlayerCounter\Enums\GameQueryType;
use ServerStatus\PlayerCounter\Models\EggGameQuery;
use ServerStatus\PlayerCounter\Models\GameQuery;
use Illuminate\Database\Seeder;

class PlayerCounterSeeder extends Seeder
{
    /**
     * Mapping of egg names/patterns to query types
     */
    protected array $eggMappings = [
        // Minecraft
        'minecraft' => 'minecraft',
        'bedrock' => 'minecraftbe',
        'pocket edition' => 'minecraftpe',
        
        // Rust
        'rust' => 'rust',
        
        // ARK
        'ark: survival evolved' => 'arkse',
        'ark: survival ascended' => 'arksa',
        
        // Source Engine Games
        'counter-strike: global offensive' => 'csgo',
        'cs:go' => 'csgo',
        'counter-strike 2' => 'csgo',
        'counter-strike: source' => 'css',
        'counter-strike 1.6' => 'cs16',
        'counter-strike 1.5' => 'cs15',
        'counter-strike: condition zero' => 'cscz',
        'team fortress 2' => 'tf2',
        'left 4 dead' => 'l4d',
        'left 4 dead 2' => 'l4d2',
        "garry's mod" => 'gmod',
        'gmod' => 'gmod',
        'half-life 2: deathmatch' => 'hl2dm',
        'half-life 1' => 'hl1',
        'half-life' => 'hl1',
        'day of defeat' => 'dod',
        'day of defeat: source' => 'dods',
        'black mesa' => 'blackmesa',
        'insurgency' => 'insurgency',
        'insurgency: sandstorm' => 'insurgencysand',
        'killing floor' => 'killingfloor',
        'killing floor 2' => 'killingfloor2',
        'no more room in hell' => 'nmrih',
        'natural selection 2' => 'ns2',
        'contagion' => 'contagion',
        'fistful of frags' => 'fof',
        'the ship' => 'ship',
        'brink' => 'brink',
        
        // Survival Games
        'valheim' => 'valheim',
        'v rising' => 'vrising',
        '7 days to die' => 'sevendaystodie',
        'conan exiles' => 'conanexiles',
        'the forest' => 'theforrest',
        'dayz' => 'dayz',
        'dayz mod' => 'dayzmod',
        'miscreated' => 'miscreated',
        'hurtworld' => 'hurtworld',
        'unturned' => 'unturned',
        'life is feudal' => 'lifeisfeudal',
        'eco' => 'eco',
        'barotrauma' => 'barotrauma',
        'avorion' => 'avorion',
        'stationeers' => 'stationeers',
        'stormworks' => 'stormworks',
        'wurm' => 'wurm',
        
        // Military Simulators
        'arma 3' => 'arma3',
        'arma 2' => 'arma',
        'arma 2: operation arrowhead' => 'armedassault2oa',
        'armed assault 3' => 'armedassault3',
        'arma' => 'arma',
        'squad' => 'squad',
        'post scriptum' => 'postscriptum',
        'hell let loose' => 'hll',
        'battalion 1944' => 'batt1944',
        'project reality' => 'projectrealitybf2',
        "america's army 3" => 'aa3',
        "america's army: proving grounds" => 'aapg',
        
        // Battlefield
        'battlefield 2' => 'bf2',
        'battlefield 3' => 'bf3',
        'battlefield 4' => 'bf4',
        'battlefield 1942' => 'bf1942',
        'battlefield: bad company 2' => 'bfbc2',
        'battlefield hardline' => 'bfh',
        
        // Call of Duty
        'call of duty' => 'cod',
        'call of duty 2' => 'cod2',
        'call of duty 4' => 'cod4',
        'call of duty: united offensive' => 'coduo',
        'call of duty: modern warfare 2' => 'codmw2',
        'call of duty: modern warfare 3' => 'codmw3',
        'call of duty: world at war' => 'codwaw',
        
        // Space Games
        'space engineers' => 'spaceengineers',
        'starmade' => 'starmade',
        'atlas' => 'atlas',
        
        // Sandbox
        'terraria' => 'terraria',
        'tshock' => 'tshock',
        
        // GTA
        'san andreas multiplayer' => 'samp',
        'sa-mp' => 'samp',
        'multi theft auto' => 'mta',
        'fivem' => 'cfx',
        'redm' => 'cfx',
        'gta5m' => 'gta5m',
        
        // Racing
        'rfactor' => 'rfactor',
        'rfactor 2' => 'rf2',
        
        // Quake Series
        'quake 2' => 'quake2',
        'quake 3' => 'quake3',
        'quake 4' => 'quake4',
        'quake live' => 'quakelive',
        'enemy territory' => 'et',
        'enemy territory: quake wars' => 'etqw',
        'wolfenstein: enemy territory' => 'et',
        
        // Unreal Series
        'unreal tournament' => 'ut',
        'unreal tournament 2004' => 'ut2004',
        'unreal tournament 3' => 'ut3',
        'unreal 2' => 'unreal2',
        'urban terror' => 'urbanterror',
        
        // Star Wars
        'jedi knight: jedi academy' => 'jediacademy',
        'jedi knight 2: jedi outcast' => 'jedioutcast',
        
        // Other FPS
        'doom 3' => 'doom3',
        'crysis' => 'crysis',
        'crysis 2' => 'crysis2',
        'crysis wars' => 'crysiswars',
        'halo' => 'halo',
        'kingpin' => 'kingpin',
        'medal of honor: allied assault' => 'mohaa',
        'soldier of fortune 2' => 'sof2',
        'serious sam' => 'serioussam',
        'swat 4' => 'swat4',
        
        // Strategy
        'dawn of war' => 'dow',
        'warsow' => 'warsow',
        
        // Other Popular
        'mordhau' => 'mordhau',
        'project zomboid' => 'zomboid',
        'red orchestra 2' => 'redorchestra2',
        'red orchestra: ostfront' => 'redorchestraostfront',
        'rising storm 2' => 'risingstorm2',
        'just cause 2' => 'justcause2',
        'just cause 3' => 'justcause3',
        'pixark' => 'pixark',
        'citadel' => 'citadel',
        'modiverse' => 'modiverse',
        'openttd' => 'openttd',
        'soldat' => 'soldat',
        'teeworlds' => 'teeworlds',
        'tibia' => 'tibia',
        'cs2d' => 'cs2d',
        
        // Voice Servers
        'teamspeak 3' => 'teamspeak3',
        'teamspeak 2' => 'teamspeak2',
        'mumble' => 'mumble',
        'ventrilo' => 'ventrilo',
    ];

    /**
     * Query port offsets for games that need them
     * Based on the $port_diff property in GameQ protocol files
     * Key: query_type, Value: port offset
     */
    protected array $queryPortOffsets = [
        // Offset +1
        'aa3' => 18243,
        'arma3' => 1,
        'armedassault2oa' => 1,
        'barotrauma' => 1,
        'brink' => 1,
        'eco' => 1,
        'egs' => 1,
        'justcause3' => 1,
        'killingfloor' => 1,
        'lhmp' => 1,
        'm2mp' => 1,
        'ns2' => 1,
        'pixark' => 1,
        'rust' => 1,
        'sco' => 1,
        'serioussam' => 1,
        'stormworks' => 1,
        'swat4' => 1,
        'theforrest' => 1,
        'ut' => 1,
        'unturned' => 1,
        'valheim' => 1,
        'vrising' => 1,
        
        // Offset +2
        'codmw3' => 2,
        'ffow' => 2,
        'lifeisfeudal' => 2,
        'miscreated' => 2,
        'rf2' => 2,
        
        // Offset +3
        'batt1944' => 3,
        'had2' => 3,
        
        // Offset +10
        'postscriptum' => 10,
        
        // Offset +15
        'hll' => 15,
        
        // Offset +29
        'insurgencysand' => 29,
        
        // Offset +101
        'terraria' => 101,
        
        // Offset +123
        'mta' => 123,
        'soldat' => 123,
        
        // Offset +8433
        'bf1942' => 8433,
        
        // Offset +13333
        'bf2' => 13333,
        
        // Offset +19238
        'arkse' => 19238,
        'killingfloor2' => 19238,
        'redorchestra2' => 19238,
        
        // Offset +19378
        'squad' => 19378,
        
        // Offset +22000
        'bf3' => 22000,
        
        // Offset +29321
        'bfbc2' => 29321,
        
        // Offset +51800
        'atlas' => 51800,
        
        // Negative offset -36938
        'mumble' => -36938,
    ];

    public function run(): void
    {
        $processedCount = 0;
        $skippedCount = 0;

        foreach (Egg::all() as $egg) {
            $queryType = $this->detectQueryType($egg);
            
            if ($queryType) {
                // Get the port offset if defined for this query type
                $portOffset = $this->queryPortOffsets[$queryType] ?? null;
                
                // Create or update the game query with port offset
                $gameQuery = GameQuery::firstOrCreate(
                    ['query_type' => $queryType],
                    ['query_port_offset' => $portOffset]
                );
                
                // Update port offset if it was created without it
                if ($portOffset !== null && $gameQuery->query_port_offset !== $portOffset) {
                    $gameQuery->update(['query_port_offset' => $portOffset]);
                }
                
                EggGameQuery::firstOrCreate([
                    'egg_id' => $egg->id,
                ], [
                    'game_query_id' => $gameQuery->id,
                ]);
                
                $processedCount++;
                
                // @phpstan-ignore if.alwaysTrue
                if ($this->command) {
                    $offsetInfo = $portOffset ? " (offset: +{$portOffset})" : "";
                    $this->command->info("Linked egg '{$egg->name}' to query type '{$queryType}'{$offsetInfo}");
                }
            } else {
                $skippedCount++;
            }
        }

        // @phpstan-ignore if.alwaysTrue
        if ($this->command) {
            $this->command->info("Processed {$processedCount} eggs, skipped {$skippedCount} eggs");
        }
    }

    /**
     * Detect the query type for an egg based on name and tags
     */
    protected function detectQueryType(Egg $egg): ?string
    {
        $eggName = strtolower($egg->name);
        $tags = array_map('strtolower', $egg->tags ?? []);

        // First, try exact name matching
        foreach ($this->eggMappings as $pattern => $queryType) {
            if (str_contains($eggName, $pattern)) {
                return $queryType;
            }
        }

        // Then try tag-based detection
        if (in_array('minecraft', $tags)) {
            if (str_contains($eggName, 'bedrock') || str_contains($eggName, 'pocket')) {
                return 'minecraftbe';
            }
            return 'minecraft';
        }

        if (in_array('source', $tags) || in_array('source_engine', $tags)) {
            // Special cases for Source engine games
            if (str_contains($eggName, 'rust')) {
                return 'rust';
            }
            return 'source';
        }

        if (in_array('voice_servers', $tags) || in_array('voice', $tags)) {
            if (str_contains($eggName, 'teamspeak')) {
                return 'teamspeak3';
            }
            if (str_contains($eggName, 'mumble')) {
                return 'mumble';
            }
        }

        return null;
    }
}
