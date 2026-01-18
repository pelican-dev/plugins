<?php

declare(strict_types=1);

namespace Starter\ServerDocumentation\Providers;

use App\Filament\Admin\Resources\Servers\ServerResource;
use App\Models\Server;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Starter\ServerDocumentation\Filament\Admin\RelationManagers\DocumentsRelationManager;
use Starter\ServerDocumentation\Models\Document;
use Starter\ServerDocumentation\Policies\DocumentPolicy;
use Starter\ServerDocumentation\Services\DocumentService;
use Starter\ServerDocumentation\Services\MarkdownConverter;

class ServerDocumentationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            plugin_path('server-documentation', 'config/server-documentation.php'),
            'server-documentation'
        );

        $this->app->singleton(DocumentService::class, function ($app) {
            return new DocumentService();
        });

        $this->app->singleton(MarkdownConverter::class, function ($app) {
            return new MarkdownConverter();
        });
    }

    public function boot(): void
    {
        Gate::policy(Document::class, DocumentPolicy::class);

        $this->registerDocumentPermissions();

        $this->loadMigrationsFrom(
            plugin_path('server-documentation', 'database/migrations')
        );

        $this->loadViewsFrom(
            plugin_path('server-documentation', 'resources/views'),
            'server-documentation'
        );

        $this->loadTranslationsFrom(
            plugin_path('server-documentation', 'lang'),
            'server-documentation'
        );

        $this->publishes([
            plugin_path('server-documentation', 'config/server-documentation.php') => config_path('server-documentation.php'),
        ], 'server-documentation-config');

        $this->publishes([
            plugin_path('server-documentation', 'resources/css') => public_path('plugins/server-documentation/css'),
        ], 'server-documentation-assets');

        Server::resolveRelationUsing('documents', function (Server $server) {
            return $server->belongsToMany(
                Document::class,
                'document_server',
                'server_id',
                'document_id'
            )->withPivot('sort_order')->withTimestamps()->orderByPivot('sort_order');
        });

        ServerResource::registerCustomRelations(DocumentsRelationManager::class);
    }

    /**
     * Register document-related Gates for admin panel permissions.
     *
     * These gates control who can manage documents in the admin panel.
     * Access is granted to:
     * - Root Admins (full access)
     * - Server Admins (users with server update/create permissions)
     *
     * Set config('server-documentation.explicit_permissions', true) to require
     * explicit document permissions instead of inheriting from server permissions.
     */
    protected function registerDocumentPermissions(): void
    {
        $permissions = [
            'viewList document',
            'view document',
            'create document',
            'update document',
            'delete document',
        ];

        foreach ($permissions as $permission) {
            Gate::define($permission, function (User $user) {
                if ($user->isRootAdmin()) {
                    return true;
                }

                if (config('server-documentation.explicit_permissions', false)) {
                    return false;
                }

                return $user->can('update server') || $user->can('create server');
            });
        }
    }
}
