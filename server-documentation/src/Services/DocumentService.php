<?php

declare(strict_types=1);

namespace Starter\ServerDocumentation\Services;

use App\Models\Server;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Starter\ServerDocumentation\Enums\DocumentType;
use Starter\ServerDocumentation\Models\Document;
use Starter\ServerDocumentation\Models\DocumentVersion;

class DocumentService
{
    /**
     * Default cache TTL in seconds (5 minutes).
     */
    private const DEFAULT_CACHE_TTL_SECONDS = 300;

    /**
     * Default badge cache TTL in seconds (1 minute).
     */
    private const DEFAULT_BADGE_CACHE_TTL_SECONDS = 60;

    /**
     * Minimum seconds between version creations (rate limiting).
     */
    private const VERSION_DEBOUNCE_SECONDS = 30;

    /**
     * Cache tag for server documents.
     */
    private const CACHE_TAG_SERVER_DOCUMENTS = 'server-documents';

    /**
     * Cache TTL for document queries in seconds.
     */
    protected int $cacheTtl;

    public function __construct()
    {
        $this->cacheTtl = config('server-documentation.cache_ttl', self::DEFAULT_CACHE_TTL_SECONDS);
    }

    /**
     * Get documents visible to a user for a specific server.
     */
    public function getDocumentsForServer(Server $server, ?User $user = null): Collection
    {
        $allowedTypes = $this->getAllowedTypesForUser($user, $server);
        $cacheKey = $this->getServerDocumentsCacheKey($server, $allowedTypes);

        if ($this->cacheTtl > 0 && $this->cacheSupportsTagging()) {
            return Cache::tags([self::CACHE_TAG_SERVER_DOCUMENTS, "server-{$server->id}"])
                ->remember($cacheKey, $this->cacheTtl, fn () => $this->queryDocumentsForServer($server, $allowedTypes));
        }

        if ($this->cacheTtl > 0) {
            return Cache::remember($cacheKey, $this->cacheTtl, fn () => $this->queryDocumentsForServer($server, $allowedTypes));
        }

        return $this->queryDocumentsForServer($server, $allowedTypes);
    }

    /**
     * Query documents for a server without caching.
     *
     * @param array<string> $allowedTypes
     */
    protected function queryDocumentsForServer(Server $server, array $allowedTypes): Collection
    {
        $attachedDocs = $server->documents()
            ->where('is_published', true)
            ->whereIn('type', $allowedTypes)
            ->orderByPivot('sort_order')
            ->get();

        $globalDocs = Document::query()
            ->where('is_global', true)
            ->where('is_published', true)
            ->whereIn('type', $allowedTypes)
            ->orderBy('sort_order')
            ->get();

        $attachedIds = $attachedDocs->pluck('id')->toArray();
        $globalDocs = $globalDocs->filter(fn ($doc) => !in_array($doc->id, $attachedIds));

        return $attachedDocs->concat($globalDocs);
    }

    /**
     * Get the document types a user can view on a specific server.
     *
     * @return array<string>
     */
    public function getAllowedTypesForUser(?User $user, Server $server): array
    {
        if ($user === null) {
            return [DocumentType::Player->value];
        }

        $level = $this->getUserHierarchyLevel($user, $server);

        return DocumentType::typesVisibleToLevel($level);
    }

    /**
     * Get the hierarchy level for a user on a specific server.
     */
    public function getUserHierarchyLevel(User $user, Server $server): int
    {
        if ($user->isRootAdmin()) {
            return DocumentType::HostAdmin->hierarchyLevel();
        }

        if ($this->isServerAdmin($user, $server)) {
            return DocumentType::ServerAdmin->hierarchyLevel();
        }

        if ($this->isServerMod($user, $server)) {
            return DocumentType::ServerMod->hierarchyLevel();
        }

        return DocumentType::Player->hierarchyLevel();
    }

    /**
     * Check if user is a Server Admin (owner or has server management permissions).
     */
    public function isServerAdmin(User $user, Server $server): bool
    {
        return $server->owner_id === $user->id ||
            $user->can('update server') ||
            $user->can('create server');
    }

    /**
     * Check if user is a Server Mod (has control permissions).
     */
    public function isServerMod(User $user, Server $server): bool
    {
        if (!enum_exists(\App\Enums\SubuserPermission::class)) {
            return false;
        }

        return $user->can(\App\Enums\SubuserPermission::ControlConsole, $server) ||
            $user->can(\App\Enums\SubuserPermission::ControlStart, $server) ||
            $user->can(\App\Enums\SubuserPermission::ControlStop, $server) ||
            $user->can(\App\Enums\SubuserPermission::ControlRestart, $server);
    }

    /**
     * Generate a change summary for version history.
     *
     * @param array<string> $dirtyFields
     */
    public function generateChangeSummary(array $dirtyFields, string $oldContent, string $newContent): string
    {
        $parts = [];

        if (in_array('title', $dirtyFields)) {
            $parts[] = 'title';
        }

        if (in_array('content', $dirtyFields)) {
            $oldLen = strlen(strip_tags($oldContent));
            $newLen = strlen(strip_tags($newContent));
            $diff = $newLen - $oldLen;

            $parts[] = match (true) {
                $diff > 0 => "content (+{$diff} chars)",
                $diff < 0 => "content ({$diff} chars)",
                default => 'content (reformatted)',
            };
        }

        return 'Updated ' . implode(', ', $parts);
    }

    /**
     * Create a version from pre-stored original values (called from model 'updated' event).
     * Includes rate limiting to prevent spam.
     */
    public function createVersionFromOriginal(
        Document $document,
        ?string $originalTitle,
        ?string $originalContent,
        ?string $changeSummary = null,
        ?int $userId = null
    ): ?DocumentVersion {
        /** @var DocumentVersion|null */
        return DB::transaction(function () use ($document, $originalTitle, $originalContent, $changeSummary, $userId): ?DocumentVersion {
            /** @var DocumentVersion|null $latestVersion */
            $latestVersion = $document->versions()
                ->lockForUpdate()
                ->latest()
                ->first();

            $latestVersionNumber = $latestVersion !== null ? $latestVersion->version_number : 0;

            if ($latestVersion !== null && $latestVersion->created_at->diffInSeconds(now()) < self::VERSION_DEBOUNCE_SECONDS) {
                $latestVersion->update([
                    'title' => $originalTitle ?? $document->title,
                    'content' => $originalContent ?? $document->content,
                    'change_summary' => $changeSummary,
                    'edited_by' => $userId ?? auth()->id(),
                ]);

                $this->logAudit('version_updated', $document, [
                    'version_number' => $latestVersion->version_number,
                    'reason' => 'rate_limited',
                ]);

                return $latestVersion;
            }

            /** @var DocumentVersion $version */
            $version = $document->versions()->create([
                'title' => $originalTitle ?? $document->title,
                'content' => $originalContent ?? $document->content,
                'version_number' => $latestVersionNumber + 1,
                'edited_by' => $userId ?? auth()->id(),
                'change_summary' => $changeSummary,
            ]);

            $this->logAudit('version_created', $document, [
                'version_number' => $version->version_number,
            ]);

            return $version;
        });
    }

    /**
     * Create a new version of a document within a transaction.
     *
     * @deprecated Use createVersionFromOriginal for model events
     */
    public function createVersion(Document $document, ?string $changeSummary = null, ?int $userId = null): DocumentVersion
    {
        /** @var DocumentVersion */
        return DB::transaction(function () use ($document, $changeSummary, $userId): DocumentVersion {
            $latestVersion = $document->versions()
                ->lockForUpdate()
                ->max('version_number') ?? 0;

            /** @var DocumentVersion */
            return $document->versions()->create([
                'title' => $document->getOriginal('title') ?? $document->title,
                'content' => $document->getOriginal('content') ?? $document->content,
                'version_number' => $latestVersion + 1,
                'edited_by' => $userId ?? auth()->id(),
                'change_summary' => $changeSummary,
            ]);
        });
    }

    /**
     * Restore a document to a previous version within a transaction.
     */
    public function restoreVersion(Document $document, DocumentVersion $version, ?int $userId = null): void
    {
        $this->logAudit('version_restore_started', $document, [
            'restoring_version' => $version->version_number,
            'current_title' => $document->title,
        ]);

        DB::transaction(function () use ($document, $version, $userId) {
            $document->updateQuietly([
                'title' => $version->title,
                'content' => $version->content,
                'last_edited_by' => $userId ?? auth()->id(),
            ]);

            $this->createVersionFromOriginal(
                $document,
                $document->title,
                $document->content,
                'Restored from version ' . $version->version_number,
                $userId
            );
        });

        $this->logAudit('version_restored', $document, [
            'restored_version' => $version->version_number,
        ]);

        $this->clearDocumentCache($document);
    }

    /**
     * Clear cache for a specific document.
     */
    public function clearDocumentCache(Document $document): void
    {
        /** @var Server $server */
        foreach ($document->servers as $server) {
            $this->clearServerDocumentsCache($server);
        }

        if ($document->is_global && $this->cacheSupportsTagging()) {
            Cache::tags([self::CACHE_TAG_SERVER_DOCUMENTS])->flush();
        }
    }

    /**
     * Clear document cache for a specific server.
     */
    public function clearServerDocumentsCache(Server $server): void
    {
        if ($this->cacheSupportsTagging()) {
            Cache::tags(["server-{$server->id}"])->flush();

            return;
        }

        foreach (DocumentType::cases() as $type) {
            $allowedTypes = DocumentType::typesVisibleToLevel($type->hierarchyLevel());
            $cacheKey = $this->getServerDocumentsCacheKey($server, $allowedTypes);
            Cache::forget($cacheKey);
        }
    }

    /**
     * Check if the cache driver supports tagging.
     */
    protected function cacheSupportsTagging(): bool
    {
        $driver = config('cache.default');

        return in_array($driver, ['redis', 'memcached', 'dynamodb', 'array']);
    }

    /**
     * Generate cache key for server documents.
     *
     * @param array<string> $allowedTypes
     */
    protected function getServerDocumentsCacheKey(Server $server, array $allowedTypes): string
    {
        sort($allowedTypes);
        $typesHash = hash('xxh3', implode(',', $allowedTypes));

        return "server-docs.{$server->id}.{$typesHash}";
    }

    /**
     * Get document count (cached for navigation badge).
     */
    public function getDocumentCount(): int
    {
        $cacheTtl = config('server-documentation.badge_cache_ttl', self::DEFAULT_BADGE_CACHE_TTL_SECONDS);

        if ($cacheTtl > 0) {
            return Cache::remember('server-docs.count', $cacheTtl, fn () => Document::count());
        }

        return Document::count();
    }

    /**
     * Clear the document count cache.
     */
    public function clearCountCache(): void
    {
        Cache::forget('server-docs.count');
    }

    /**
     * Prune old versions keeping only the specified number of recent versions.
     */
    public function pruneVersions(Document $document, ?int $keepCount = null): int
    {
        $keepCount ??= config('server-documentation.versions_to_keep', 50);

        if ($keepCount <= 0) {
            return 0;
        }

        $versionsToKeep = $document->versions()
            ->orderByDesc('version_number')
            ->limit($keepCount)
            ->pluck('id');

        $deleted = $document->versions()
            ->whereNotIn('id', $versionsToKeep)
            ->delete();

        if ($deleted > 0) {
            $this->logAudit('versions_pruned', $document, [
                'deleted_count' => $deleted,
                'kept_count' => $keepCount,
            ]);
        }

        return $deleted;
    }

    /**
     * Log an audit event for document operations.
     *
     * @param array<string, mixed> $context
     */
    protected function logAudit(string $action, Document $document, array $context = []): void
    {
        $context = array_merge([
            'document_id' => $document->id,
            'document_title' => $document->title,
            'user_id' => auth()->id(),
            'user' => auth()->user()?->username,
        ], $context);

        Log::channel(config('server-documentation.audit_log_channel', 'single'))
            ->info("Document {$action}", $context);
    }
}
