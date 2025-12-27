<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('server_folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('color')->nullable();
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('server_folder_server', function (Blueprint $table) {
            $table->foreignId('folder_id')->constrained('server_folders')->cascadeOnDelete();
            $table->foreignId('server_id')->constrained()->cascadeOnDelete();
            $table->primary(['folder_id', 'server_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('server_folder_server');
        Schema::dropIfExists('server_folders');
    }
};
