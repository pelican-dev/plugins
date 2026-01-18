<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->longText('content');
            $table->unsignedInteger('version_number');
            $table->unsignedBigInteger('edited_by')->nullable();
            $table->string('change_summary')->nullable();
            $table->timestamps();

            $table->foreign('edited_by')->references('id')->on('users')->nullOnDelete();
            $table->index(['document_id', 'version_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_versions');
    }
};
