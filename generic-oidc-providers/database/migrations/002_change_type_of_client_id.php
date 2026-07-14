<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('generic_oidc_providers', function (Blueprint $table) {
            $table->text('client_id')->change();
        });
    }

    public function down(): void
    {
        Schema::table('generic_oidc_providers', function (Blueprint $table) {
            $table->string('client_id')->change();
        });
    }
};
