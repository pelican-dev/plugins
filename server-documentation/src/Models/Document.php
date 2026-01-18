<?php

declare(strict_types=1);

namespace Starter\ServerDocumentation\Models;

use App\Models\Server;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Starter\ServerDocumentation\Database\Factories\DocumentFactory;
use Starter\ServerDocumentation\Enums\DocumentType;
use Starter\ServerDocumentation\Services\DocumentService;

/**
 * @property int $id
 * @property string $uuid
 * @property string $title
 * @property string $slug
 * @property string $content
 * @property string $type
 * @property bool $is_global
 * @property bool $is_published
 * @property int|null $author_id
 * @property int|null $last_edited_by
 * @property int $sort_order
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read User|null $author
 * @property-read User|null $lastEditor
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Server> $servers
 * @property-read \Illuminate\Database\Eloquent\Collection<int, DocumentVersion> $versions
 *
 * @method static Builder|Document forServer(Server $server)
 * @method static Builder|Document forTypes(array $types)
 * @method static Builder|Document published()
 * @method static Builder|Document global()
 * @method static Builder|Document search(string $term)
 * @method static Builder|Document visibleTo(?User $user, Server $server)
 */
class Document extends Model
{
    /** @use HasFactory<DocumentFactory> */
    use HasFactory;
    use SoftDeletes;

    protected static function newFactory(): DocumentFactory
    {
        return DocumentFactory::new();
    }

    /**
     * Resource name for API/permission references.
     */
    public const RESOURCE_NAME = 'document';

    /**
     * Temporary storage for original values before update (for versioning).
     *
     * @var array{title?: string, content?: string, dirty_fields?: array<string>}
     */
    protected array $originalValuesForVersion = [];

    protected $table = 'documents';

    protected $fillable = [
        'uuid',
        'title',
        'slug',
        'content',
        'type',
        'is_global',
        'is_published',
        'author_id',
        'last_edited_by',
        'sort_order',
    ];

    /**
     * Validation rules for the model.
     *
     * @var array<string, array<string>>
     */
    public static array $validationRules = [
        'title' => ['required', 'string', 'max:255'],
        'slug' => ['required', 'string', 'max:255', 'alpha_dash'],
        'content' => ['required', 'string'],
        'type' => ['required', 'string', 'in:host_admin,server_admin,server_mod,player'],
        'is_global' => ['boolean'],
        'is_published' => ['boolean'],
        'sort_order' => ['integer'],
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_global' => 'boolean',
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Document $document) {
            $document->uuid ??= Str::uuid()->toString();
            if ($document->slug === null) {
                $document->slug = static::generateUniqueSlug($document->title);
            }
            if ($document->author_id === null && auth()->check()) {
                $document->author_id = auth()->id();
            }
        });

        static::updating(function (Document $document) {
            if ($document->isDirty(['title', 'content'])) {
                $document->originalValuesForVersion = [
                    'title' => $document->getOriginal('title'),
                    'content' => $document->getOriginal('content'),
                    'dirty_fields' => array_keys($document->getDirty()),
                ];

                if (auth()->check()) {
                    $document->last_edited_by = auth()->id();
                }
            }
        });

        static::updated(function (Document $document) {
            if (!empty($document->originalValuesForVersion)) {
                $changeSummary = app(DocumentService::class)->generateChangeSummary(
                    $document->originalValuesForVersion['dirty_fields'] ?? [],
                    $document->originalValuesForVersion['content'] ?? '',
                    $document->content
                );

                app(DocumentService::class)->createVersionFromOriginal(
                    $document,
                    $document->originalValuesForVersion['title'],
                    $document->originalValuesForVersion['content'],
                    $changeSummary
                );

                $document->originalValuesForVersion = [];
            }
        });

        static::saved(function (Document $document) {
            app(DocumentService::class)->clearDocumentCache($document);
            app(DocumentService::class)->clearCountCache();

            if (config('server-documentation.auto_prune_versions', false)) {
                app(DocumentService::class)->pruneVersions($document);
            }
        });

        static::deleted(function (Document $document) {
            app(DocumentService::class)->clearCountCache();
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function lastEditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_edited_by');
    }

    public function servers(): BelongsToMany
    {
        return $this->belongsToMany(Server::class, 'document_server')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class)
            ->orderByDesc('version_number');
    }

    public function createVersion(?string $changeSummary = null): DocumentVersion
    {
        return app(DocumentService::class)->createVersion($this, $changeSummary);
    }

    public function restoreVersion(DocumentVersion $version): void
    {
        app(DocumentService::class)->restoreVersion($this, $version);
    }

    public function getCurrentVersionNumber(): int
    {
        return $this->versions()->max('version_number') ?? 1;
    }

    public function scopeHostAdmin(Builder $query): Builder
    {
        return $query->where('type', DocumentType::HostAdmin->value);
    }

    public function scopeServerAdmin(Builder $query): Builder
    {
        return $query->whereIn('type', [DocumentType::ServerAdmin->value, DocumentType::LEGACY_ADMIN]);
    }

    public function scopeServerMod(Builder $query): Builder
    {
        return $query->where('type', DocumentType::ServerMod->value);
    }

    public function scopePlayer(Builder $query): Builder
    {
        return $query->where('type', DocumentType::Player->value);
    }

    public function scopeGlobal(Builder $query): Builder
    {
        return $query->where('is_global', true);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeForServer(Builder $query, Server $server): Builder
    {
        return $query->where(function (Builder $q) use ($server) {
            $q->whereHas('servers', fn (Builder $sub) => $sub->where('servers.id', $server->id))
                ->orWhere('is_global', true);
        });
    }

    /**
     * @param array<string> $types
     */
    public function scopeForTypes(Builder $query, array $types): Builder
    {
        return $query->whereIn('type', $types);
    }

    /**
     * Search documents by title, slug, or content.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        $term = trim($term);
        if (empty($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('slug', 'like', "%{$term}%")
              ->orWhere('content', 'like', "%{$term}%");
        });
    }

    /**
     * Scope to documents visible to a specific user on a server.
     */
    public function scopeVisibleTo(Builder $query, ?User $user, Server $server): Builder
    {
        $allowedTypes = app(DocumentService::class)->getAllowedTypesForUser($user, $server);

        return $query
            ->forServer($server)
            ->published()
            ->forTypes($allowedTypes);
    }

    public function getDocumentType(): ?DocumentType
    {
        return DocumentType::tryFromLegacy($this->type);
    }

    public function isHostAdminOnly(): bool
    {
        return $this->getDocumentType() === DocumentType::HostAdmin;
    }

    public function isServerAdminOnly(): bool
    {
        return $this->getDocumentType() === DocumentType::ServerAdmin;
    }

    public function isServerModOnly(): bool
    {
        return $this->getDocumentType() === DocumentType::ServerMod;
    }

    public function isPlayerVisible(): bool
    {
        return $this->getDocumentType() === DocumentType::Player;
    }

    /**
     * Get the minimum tier required to view this document.
     */
    public function getRequiredTier(): int
    {
        return $this->getDocumentType()?->hierarchyLevel() ?? DocumentType::Player->hierarchyLevel();
    }

    /**
     * Check if a user with the given tier can view this document.
     */
    public function isVisibleToTier(int $tier): bool
    {
        return $tier >= $this->getRequiredTier();
    }

    /**
     * Generate a unique slug from a title.
     */
    protected static function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (static::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        return $slug;
    }
}
