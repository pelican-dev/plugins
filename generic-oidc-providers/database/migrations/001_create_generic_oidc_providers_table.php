<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generic_oidc_providers', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->boolean('create_missing_users')->default(false);
            $table->boolean('link_missing_users')->default(false);
            $table->string('display_name');
            $table->string('display_icon')->nullable();
            $table->string('display_color', 7)->nullable();
            $table->string('base_url');
            $table->string('client_id');
            $table->text('client_secret');
            $table->boolean('verify_jwt')->default(false);
            $table->text('jwt_public_key')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generic_oidc_providers');
    }
};
