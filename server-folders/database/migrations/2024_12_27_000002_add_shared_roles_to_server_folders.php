<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('server_folders', function (Blueprint $table) {
            $table->boolean('is_shared')->default(false)->after('sort_order');
        });

        Schema::create('server_folder_role', function (Blueprint $table) {
            $table->foreignId('folder_id')->constrained('server_folders')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->primary(['folder_id', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('server_folder_role');

        Schema::table('server_folders', function (Blueprint $table) {
            $table->dropColumn('is_shared');
        });
    }
};
