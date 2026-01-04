<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Can safely delete any Cloudflare domains without IDs as they're useless anyway
        DB::table('cloudflare_domains')->whereNull('cloudflare_id')->delete();
        DB::table('subdomains')->whereNull('cloudflare_id')->delete();

        Schema::table('cloudflare_domains', function (Blueprint $table) {
            $table->dropColumn('srv_target');
            $table->string('cloudflare_id')->nullable(false)->change();
        });

        Schema::table('subdomains', function (Blueprint $table) {
            $table->string('cloudflare_id')->nullable(false)->change();
        });

        Schema::table('nodes', function (Blueprint $table) {
            $table->string('srv_target')->nullable()->after('fqdn');
        });
    }

    public function down(): void
    {
        Schema::table('cloudflare_domains', function (Blueprint $table) {
            $table->string('srv_target')->nullable()->after('cloudflare_id');
            $table->string('cloudflare_id')->nullable()->change();
        });

        Schema::table('subdomains', function (Blueprint $table) {
            $table->string('cloudflare_id')->nullable()->change();
        });

        Schema::table('nodes', function (Blueprint $table) {
            $table->dropColumn('srv_target');
        });
    }
};
