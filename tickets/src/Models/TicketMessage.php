<?php

namespace Boy132\Tickets\Models;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $message
 * @property bool $hidden
 * @property int $ticket_id
 * @property Ticket $ticket
 * @property ?int $author_id
 * @property ?User $author
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class TicketMessage extends Model
{
    protected $fillable = [
        'message',
        'hidden',
        'ticket_id',
        'author_id',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model) {
            $model->author_id ??= auth()->user()?->id;
        });
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
