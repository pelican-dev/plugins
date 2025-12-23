<?php

namespace Boy132\UserCreatableServers\Providers;

use App\Enums\HeaderActionPosition;
use App\Enums\HeaderWidgetPosition;
use App\Filament\Admin\Resources\Users\UserResource;
use App\Filament\App\Resources\Servers\Pages\ListServers;
use App\Models\Role;
use App\Models\User;
use Boy132\UserCreatableServers\Filament\Admin\Resources\Users\RelationManagers\UserResourceLimitRelationManager;
use Boy132\UserCreatableServers\Filament\App\Widgets\UserResourceLimitsOverview;
use Boy132\UserCreatableServers\Filament\Components\Actions\CreateServerAction;
use Boy132\UserCreatableServers\Models\UserResourceLimits;
use Illuminate\Support\ServiceProvider;

class UserCreatableServersPluginProvider extends ServiceProvider
{
    public function register(): void
    {
        UserResource::registerCustomRelations(UserResourceLimitRelationManager::class);

        ListServers::registerCustomHeaderWidgets(HeaderWidgetPosition::Before, UserResourceLimitsOverview::class);

        ListServers::registerCustomHeaderActions(HeaderActionPosition::Before, CreateServerAction::make());

        Role::registerCustomDefaultPermissions('userResourceLimits');
        Role::registerCustomModelIcon('userResourceLimits', 'tabler-cube-plus');
    }

    public function boot(): void
    {
        User::resolveRelationUsing('userResourceLimits', fn (User $user) => $user->belongsTo(UserResourceLimits::class, 'id', 'user_id'));
    }
}
