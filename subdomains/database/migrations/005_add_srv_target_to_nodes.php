<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cloudflare_domains', function (Blueprint $table) {
            $table->dropColumn('srv_target');
        });

        Schema::table('nodes', function (Blueprint $table) {
            $table->string('srv_target')->nullable()->after('fqdn');
        });
    }

    public function down(): void
    {
        Schema::table('cloudflare_domains', function (Blueprint $table) {
            $table->string('srv_target')->nullable()->after('cloudflare_id');
        });

        Schema::table('nodes', function (Blueprint $table) {
            $table->dropColumn('srv_target');
        });
    }
};
