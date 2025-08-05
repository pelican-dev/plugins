<?php

use App\Models\Egg;
use Boy132\PlayerCounter\Models\GameQuery;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('egg_game_query', function (Blueprint $table) {
            $table->foreignIdFor(Egg::class, 'egg_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(GameQuery::class, 'game_query_id')->constrained()->cascadeOnDelete();

            $table->unique('egg_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('egg_game_query');
    }
};
