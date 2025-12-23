<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('category');
            $table->string('priority');
            $table->text('description')->nullable();
            $table->boolean('is_answered')->default(false);
            $table->text('answer')->nullable();
            $table->unsignedInteger('server_id');
            $table->foreign('server_id')->references('id')->on('servers')->cascadeOnDelete();
            $table->unsignedInteger('author_id');
            $table->foreign('author_id')->references('id')->on('users')->nullOnDelete();
            $table->unsignedInteger('assigned_user_id');
            $table->foreign('assigned_user_id')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
