<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Add performance indexes for common queries
            $table->index(['is_published', 'type'], 'idx_documents_published_type');
            $table->index(['is_global', 'is_published'], 'idx_documents_global_published');
            $table->index('sort_order', 'idx_documents_sort');
        });

        // Fix slug uniqueness to allow reuse after soft delete
        // This requires database-specific handling
        $driver = DB::getDriverName();

        Schema::table('documents', function (Blueprint $table) {
            // First, drop the existing unique constraint
            $table->dropUnique(['slug']);
        });

        if ($driver === 'mysql' || $driver === 'mariadb') {
            // MySQL/MariaDB: Use a partial unique index workaround
            // Create a generated column that's null when deleted
            DB::statement('ALTER TABLE documents ADD COLUMN slug_unique VARCHAR(255) GENERATED ALWAYS AS (IF(deleted_at IS NULL, slug, NULL)) STORED');
            DB::statement('CREATE UNIQUE INDEX idx_documents_slug_active ON documents(slug_unique)');
        } elseif ($driver === 'pgsql') {
            // PostgreSQL: Use a partial unique index
            DB::statement('CREATE UNIQUE INDEX idx_documents_slug_active ON documents(slug) WHERE deleted_at IS NULL');
        } elseif ($driver === 'sqlite') {
            // SQLite 3.9+: Use a partial unique index
            DB::statement('CREATE UNIQUE INDEX idx_documents_slug_active ON documents(slug) WHERE deleted_at IS NULL');
        } else {
            // Fallback for unsupported drivers: regular unique (slug reuse after soft delete won't work)
            Schema::table('documents', function (Blueprint $table) {
                $table->unique('slug');
            });
        }

        // Add unique constraint on document versions to prevent race condition duplicates
        Schema::table('document_versions', function (Blueprint $table) {
            $table->unique(['document_id', 'version_number'], 'idx_document_versions_unique');
        });
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        // Remove version unique constraint
        Schema::table('document_versions', function (Blueprint $table) {
            $table->dropUnique('idx_document_versions_unique');
        });

        // Remove slug constraint based on driver
        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('DROP INDEX idx_documents_slug_active ON documents');
            DB::statement('ALTER TABLE documents DROP COLUMN slug_unique');
        } elseif ($driver === 'pgsql') {
            DB::statement('DROP INDEX idx_documents_slug_active');
        } elseif ($driver === 'sqlite') {
            DB::statement('DROP INDEX idx_documents_slug_active');
        } else {
            Schema::table('documents', function (Blueprint $table) {
                $table->dropUnique(['slug']);
            });
        }

        // Restore original unique constraint
        Schema::table('documents', function (Blueprint $table) {
            $table->unique('slug');
        });

        // Remove performance indexes
        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex('idx_documents_published_type');
            $table->dropIndex('idx_documents_global_published');
            $table->dropIndex('idx_documents_sort');
        });
    }
};
