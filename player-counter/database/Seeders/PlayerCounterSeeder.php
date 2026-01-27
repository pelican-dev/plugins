<?php

namespace Database\Seeders;

use App\Models\Egg;
use Boy132\PlayerCounter\Models\EggGameQuery;
use Boy132\PlayerCounter\Models\GameQuery;
use Exception;
use Illuminate\Database\Seeder;

class PlayerCounterSeeder extends Seeder
{
    public const MAPPINGS = [
        [
            'names' => 'Squad',
            'query_type' => 'source',
            'query_port_offset' => 19378,
        ],
        [
            'names' => 'Barotrauma',
            'query_type' => 'source',
            'query_port_offset' => 1,
        ],
        [
            'names' => 'Valheim',
            'query_type' => 'source',
            'query_port_offset' => 1,
        ],
        [
            'names' => ['V Rising', 'V-Rising', 'VRising'],
            'query_type' => 'source',
            'query_port_offset' => 1,
        ],
        [
            'names' => ['The Forrest', 'TheForrest'],
            'query_type' => 'source',
            'query_port_offset' => 1,
        ],
        [
            'names' => ['Arma 3', 'Arma3'],
            'query_type' => 'source',
            'query_port_offset' => 1,
        ],
        [
            'names' => ['ARK: Survival Evolved', 'ARK: SurvivalEvolved', 'ARK Survival Evolved', 'ARK SurvivalEvolved', 'ARKSurvivalEvolved'],
            'query_type' => 'source',
            'query_port_offset' => 19238,
        ],
        [
            'names' => 'Unturned',
            'query_type' => 'source',
            'query_port_offset' => 1,
        ],
        [
            'names' => ['Insurgency: Sandstorm', 'Insurgency Sandstorm', 'InsurgencySandstorm'],
            'query_type' => 'source',
            'query_port_offset' => 29,
        ],
        [
            'tag' => 'bedrock',
            'query_type' => 'minecraft_bedrock',
            'query_port_offset' => null,
        ],
        [
            'tag' => 'minecraft',
            'query_type' => 'minecraft_java',
            'query_port_offset' => null,
        ],
        [
            'tag' => 'source',
            'query_type' => 'source',
            'query_port_offset' => null,
        ],
    ];

    public function run(): void
    {
        foreach (Egg::all() as $egg) {
            $tags = $egg->tags ?? [];

            foreach (self::MAPPINGS as $mapping) {
                if ((array_key_exists('names', $mapping) && in_array($egg->name, array_wrap($mapping['names']))) || (array_key_exists('tag', $mapping) && in_array($mapping['tag'], $tags))) {
                    try {
                        $query = GameQuery::firstOrCreate([
                            'query_type' => $mapping['query_type'],
                            'query_port_offset' => $mapping['query_port_offset'],
                        ]);

                        EggGameQuery::firstOrCreate([
                            'egg_id' => $egg->id,
                        ], [
                            'game_query_id' => $query->id,
                        ]);
                    } catch (Exception) {
                    }
                }
            }
        }

        // @phpstan-ignore if.alwaysTrue
        if ($this->command) {
            $this->command->info('Created game query types for existing eggs');
        }
    }
}
