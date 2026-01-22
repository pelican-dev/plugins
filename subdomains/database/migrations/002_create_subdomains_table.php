<?php

use Boy132\Subdomains\Models\Subdomain;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subdomains', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('record_type')->default('A');
            $table->string('cloudflare_id')->nullable();

            $table->unsignedInteger('domain_id');
            $table->foreign('domain_id')->references('id')->on('cloudflare_domains')->cascadeOnDelete();

            $table->unsignedInteger('server_id');
            $table->foreign('server_id')->references('id')->on('servers')->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['name', 'domain_id']);
        });
    }

    public function down(): void
    {
        Subdomain::all()->each(function (Subdomain $subdomain) {
            try {
                $subdomain->delete();
            } catch (Exception) {
            }
        });

        Schema::dropIfExists('subdomains');
    }
};
