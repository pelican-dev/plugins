<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        // Change enum to string for flexibility with new document types
        if ($driver === 'sqlite') {
            // SQLite doesn't support ALTER COLUMN, but it also doesn't enforce enum types
            // The column will accept any value, so we just need to update the data
        } elseif ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('ALTER TABLE documents MODIFY COLUMN type VARCHAR(50) NOT NULL DEFAULT \'player\'');
        } elseif ($driver === 'pgsql') {
            // PostgreSQL: drop the enum constraint and change to varchar
            DB::statement('ALTER TABLE documents ALTER COLUMN type TYPE VARCHAR(50)');
            DB::statement('ALTER TABLE documents ALTER COLUMN type SET DEFAULT \'player\'');
        }

        // Migrate old 'admin' type to 'server_admin' for all drivers
        DB::table('documents')->where('type', 'admin')->update(['type' => 'server_admin']);
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        // Migrate back to old types
        DB::table('documents')->where('type', 'server_admin')->update(['type' => 'admin']);
        DB::table('documents')->whereIn('type', ['host_admin', 'server_mod'])->update(['type' => 'admin']);

        // Change back to enum (MySQL only - other drivers will just have varchar)
        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('ALTER TABLE documents MODIFY COLUMN type ENUM(\'admin\', \'player\') NOT NULL DEFAULT \'player\'');
        }
    }
};
