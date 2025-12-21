<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('server_server_tag', function (Blueprint $table) {
            $table->unsignedInteger('server_id');
            $table->foreign('server_id')->references('id')->on('servers')->cascadeOnDelete();

            $table->unsignedInteger('server_tag_id');
            $table->foreign('server_tag_id')->references('id')->on('server_tags')->cascadeOnDelete();

            $table->primary(['server_id', 'server_tag_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('server_server_tag');
    }
};