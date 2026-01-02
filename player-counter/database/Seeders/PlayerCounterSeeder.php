<?php

namespace Database\Seeders;

use App\Models\Egg;
use Boy132\PlayerCounter\Models\EggGameQuery;
use Boy132\PlayerCounter\Models\GameQuery;
use Illuminate\Database\Seeder;

class PlayerCounterSeeder extends Seeder
{
    public function run(): void
    {
        $minecraftQuery = GameQuery::firstOrCreate(['query_type' => 'minecraft']);
        $sourceQuery = GameQuery::firstOrCreate(['query_type' => 'source']);

        foreach (Egg::all() as $egg) {
            $tags = $egg->tags ?? [];

            if (in_array('minecraft', $tags)) {
                EggGameQuery::firstOrCreate([
                    'egg_id' => $egg->id,
                ], [
                    'game_query_id' => $minecraftQuery->id,
                ]);
            } elseif (in_array('source', $tags)) {
                EggGameQuery::firstOrCreate([
                    'egg_id' => $egg->id,
                ], [
                    'game_query_id' => $sourceQuery->id,
                ]);
            }
        }

        // @phpstan-ignore if.alwaysTrue
        if ($this->command) {
            $this->command->info('Created game query types for minecraft and source');
        }
    }
}
