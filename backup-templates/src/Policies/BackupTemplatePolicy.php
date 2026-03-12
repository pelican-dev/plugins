<?php

namespace Ebnater\BackupTemplates\Policies;

use App\Enums\SubuserPermission;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

class BackupTemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(SubuserPermission::BackupRead, Filament::getTenant());
    }

    public function view(User $user, Model $model): bool
    {
        return $user->can(SubuserPermission::BackupRead, Filament::getTenant());
    }

    public function create(User $user): bool
    {
        return $user->can('backupTemplates.create', Filament::getTenant());
    }

    public function update(User $user, Model $model): bool
    {
        return $user->can('backupTemplates.create', Filament::getTenant());
    }

    public function delete(User $user, Model $model): bool
    {
        return $user->can('backupTemplates.create', Filament::getTenant());
    }
}
