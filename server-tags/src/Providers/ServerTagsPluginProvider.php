<?php

namespace Boy132\ServerTags\Providers;

use App\Filament\Admin\Resources\Servers\ServerResource;
use App\Models\Role;
use Boy132\ServerTags\Filament\Admin\Resources\Servers\RelationManagers\ServerTagRelationManager;
use Illuminate\Support\ServiceProvider;

class ServerTagsPluginProvider extends ServiceProvider
{
    public function register(): void
    {
        Role::registerCustomDefaultPermissions('server_tag');
        Role::registerCustomModelIcon('server_tag', 'tabler-tags');

        ServerResource::registerCustomRelations(ServerTagRelationManager::class);
    }

    public function boot(): void {}
}