<?php

namespace FlexKleks\ServerFolders\Models;

use App\Models\Role;
use App\Models\Server;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ServerFolder extends Model
{
    protected $table = 'server_folders';

    protected $fillable = [
        'user_id',
        'name',
        'color',
        'icon',
        'sort_order',
        'is_shared',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_shared' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function servers(): BelongsToMany
    {
        return $this->belongsToMany(Server::class, 'server_folder_server', 'folder_id', 'server_id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'server_folder_role', 'folder_id', 'role_id');
    }

    /**
     * Scope to get folders visible to a user (own folders + shared folders with matching roles)
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $query->where(function ($q) use ($user) {
            // User's own folders
            $q->where('user_id', $user->id)
                // OR shared folders where user has a matching role
                ->orWhere(function ($q2) use ($user) {
                    $q2->where('is_shared', true)
                        ->whereHas('roles', function ($q3) use ($user) {
                            $q3->whereIn('roles.id', $user->roles->pluck('id'));
                        });
                });
        });
    }

    /**
     * Check if a user can view this folder
     */
    public function isVisibleTo(User $user): bool
    {
        // Owner can always see
        if ($this->user_id === $user->id) {
            return true;
        }

        // Check if shared and user has matching role
        if ($this->is_shared) {
            $userRoleIds = $user->roles->pluck('id')->toArray();
            $folderRoleIds = $this->roles->pluck('id')->toArray();

            return count(array_intersect($userRoleIds, $folderRoleIds)) > 0;
        }

        return false;
    }

    /**
     * Check if a user can edit this folder (only owner)
     */
    public function isEditableBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }
}
