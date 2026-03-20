<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhooks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('webhook_url');
            $table->foreignId('server_id')->nullable()->constrained('servers')->nullOnDelete();
            $table->json('events')->default('[]');
            $table->boolean('enabled')->default(true);
            $table->timestamp('last_triggered_at')->nullable();
            $table->timestamps();

            // foreignId above handles FK constraint and nullOnDelete
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhooks');
    }
};
