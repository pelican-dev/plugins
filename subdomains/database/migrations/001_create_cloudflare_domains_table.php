<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cloudflare_domains', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('cloudflare_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cloudflare_domains');
    }
};
