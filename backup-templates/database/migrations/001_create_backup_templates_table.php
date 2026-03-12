<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backup_templates', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('server_id');
            $table->foreign('server_id')->references('id')->on('servers')->cascadeOnDelete();

            $table->string('name');
            $table->text('ignored')->nullable();

            $table->timestamps();

            $table->unique(['server_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_templates');
    }
};
