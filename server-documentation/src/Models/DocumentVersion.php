<?php

declare(strict_types=1);

namespace Starter\ServerDocumentation\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Starter\ServerDocumentation\Database\Factories\DocumentVersionFactory;

/**
 * @property int $id
 * @property int $document_id
 * @property string $title
 * @property string $content
 * @property int $version_number
 * @property int|null $edited_by
 * @property string|null $change_summary
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read Document $document
 * @property-read User|null $editor
 * @property-read string $formatted_version
 */
class DocumentVersion extends Model
{
    /** @use HasFactory<DocumentVersionFactory> */
    use HasFactory;

    protected static function newFactory(): DocumentVersionFactory
    {
        return DocumentVersionFactory::new();
    }

    protected $table = 'document_versions';

    protected $fillable = [
        'document_id',
        'title',
        'content',
        'version_number',
        'edited_by',
        'change_summary',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'version_number' => 'integer',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    public function getFormattedVersionAttribute(): string
    {
        return 'v' . $this->version_number;
    }
}
