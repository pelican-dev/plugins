<?php

namespace Boy132\ServerTags;

use App\Filament\Admin\Resources\Servers\ServerResource;
use App\Models\Role;
use App\Models\Server;
use Boy132\ServerTags\Filament\Admin\Resources\Servers\RelationManagers\ServerTagRelationManager;
use Boy132\ServerTags\Models\ServerTag;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ServerTagsPlugin implements Plugin
{
    public function getId(): string
    {
        return 'server-tags';
    }

    public function register(Panel $panel): void
    {
        $id = str($panel->getId())->title();

        $panel->discoverResources(plugin_path($this->getId(), "src/Filament/$id/Resources"), "Boy132\\ServerTags\\Filament\\$id\\Resources");

        // Register the relation manager and permissions directly here
        if ($panel->getId() === 'admin') {
            Role::registerCustomDefaultPermissions('server_tag');
            Role::registerCustomModelIcon('server_tag', 'tabler-tags');

            try {
                ServerResource::registerCustomRelations(ServerTagRelationManager::class);
            } catch (\Exception $e) {
                // Silently fail if already registered
            }
        }

        if ($panel->getId() === 'app') {
            // NOTE: To use the tagged servers page, manually replace 'ListServers' with 'ListTaggedServers' in your App\Filament\App\Resources\Servers\ServerResource::getPages() method.
        }
    }

    public function boot(Panel $panel): void
    {
        // Add relationship to Server model dynamically
        if (!method_exists(Server::class, 'serverTags')) {
            Server::resolveRelationUsing('serverTags', function ($serverModel) {
                return $serverModel->belongsToMany(ServerTag::class, 'server_server_tag');
            });
        }
    }

    public static function getServerTags(Server $server): BelongsToMany
    {
        return $server->belongsToMany(ServerTag::class, 'server_server_tag');
    }
}