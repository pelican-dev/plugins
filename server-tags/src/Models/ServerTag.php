<?php

namespace Boy132\ServerTags\Models;

use App\Models\Server;
use Carbon\Carbon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $color
 * @property ?string $description
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Collection|Server[] $servers
 * @property int|null $servers_count
 */
class ServerTag extends Model implements HasLabel
{
    protected $fillable = [
        'name',
        'slug',
        'color',
        'description',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model) {
            if (!$model->slug) {
                $model->slug = Str::slug($model->name);
            }
        });

        static::updating(function (self $model) {
            if ($model->isDirty('name') && !$model->isDirty('slug')) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    public function servers(): BelongsToMany
    {
        return $this->belongsToMany(Server::class, 'server_server_tag');
    }

    public function getLabel(): string
    {
        return $this->name;
    }
}