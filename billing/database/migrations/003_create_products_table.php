<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('stripe_id')->nullable();
            $table->string('name');
            $table->text('description');
            $table->unsignedInteger('cpu')->default(0);
            $table->unsignedInteger('memory')->default(0);
            $table->unsignedInteger('disk')->default(0);
            $table->unsignedInteger('swap')->default(0);
            $table->json('ports');
            $table->json('tags');
            $table->unsignedInteger('allocation_limit')->default(0);
            $table->unsignedInteger('database_limit')->default(0);
            $table->unsignedInteger('backup_limit')->default(0);

            $table->unsignedInteger('egg_id');
            $table->foreign('egg_id')->references('id')->on('eggs')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
