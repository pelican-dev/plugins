<?php

declare(strict_types=1);

namespace Starter\ServerDocumentation\Policies;

use App\Models\Server;
use App\Models\User;
use Starter\ServerDocumentation\Models\Document;
use Starter\ServerDocumentation\Services\DocumentService;

class DocumentPolicy
{
    /**
     * Admin panel: Can user view documents list?
     * Uses Pelican's space-separated permission pattern.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('viewList document');
    }

    /**
     * Admin panel: Can user view a specific document?
     */
    public function view(User $user, Document $document): bool
    {
        return $user->can('view document');
    }

    /**
     * Admin panel: Can user create documents?
     */
    public function create(User $user): bool
    {
        return $user->can('create document');
    }

    /**
     * Admin panel: Can user update documents?
     */
    public function update(User $user, Document $document): bool
    {
        return $user->can('update document');
    }

    /**
     * Admin panel: Can user delete documents?
     */
    public function delete(User $user, Document $document): bool
    {
        return $user->can('delete document');
    }

    /**
     * Admin panel: Can user restore soft-deleted documents?
     */
    public function restore(User $user, Document $document): bool
    {
        return $user->can('delete document');
    }

    /**
     * Admin panel: Can user permanently delete documents?
     */
    public function forceDelete(User $user, Document $document): bool
    {
        return $user->can('delete document');
    }

    /**
     * Server panel: Can user view this document on a specific server?
     * Implements 4-tier permission hierarchy:
     * - host_admin: Root Admin only
     * - server_admin: Server owner OR admin with update/create server permission
     * - server_mod: Subusers with control permissions
     * - player: Anyone with server access
     */
    public function viewOnServer(User $user, Document $document, Server $server): bool
    {
        if ($user->isRootAdmin()) {
            if (!$document->is_global && !$document->servers()->where('servers.id', $server->id)->exists()) {
                return false;
            }

            return true;
        }

        if (!$document->is_published) {
            return false;
        }

        if (!$document->is_global && !$document->servers()->where('servers.id', $server->id)->exists()) {
            return false;
        }

        $allowedTypes = app(DocumentService::class)->getAllowedTypesForUser($user, $server);

        return in_array($document->type, $allowedTypes);
    }
}
