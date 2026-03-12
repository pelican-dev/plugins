<?php

namespace Ebnater\BackupTemplates\Models;

use App\Models\Server;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $server_id
 * @property string $name
 * @property string|null $ignored
 * @property Server $server
 */
class BackupTemplate extends Model
{
    protected $fillable = [
        'server_id',
        'name',
        'ignored',
    ];

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class, 'server_id');
    }
}
