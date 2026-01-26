<?php

namespace Boy132\Tickets\Models;

use App\Models\Server;
use App\Models\User;
use Boy132\Tickets\Enums\TicketCategory;
use Boy132\Tickets\Enums\TicketPriority;
use Boy132\Tickets\Enums\TicketStatus;
use Boy132\Tickets\Filament\Server\Resources\Tickets\Pages\ListTickets;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Support\Markdown;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $title
 * @property TicketCategory $category
 * @property TicketPriority $priority
 * @property TicketStatus $status
 * @property ?string $description
 * @property int $server_id
 * @property Server $server
 * @property ?int $author_id
 * @property ?User $author
 * @property ?int $assigned_user_id
 * @property ?User $assignedUser
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Ticket extends Model
{
    protected $fillable = [
        'title',
        'category',
        'priority',
        'status',
        'description',
        'server_id',
        'author_id',
        'assigned_user_id',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model) {
            $model->status = TicketStatus::Open;

            $model->server_id ??= Filament::getTenant()?->getKey();
            $model->author_id ??= auth()->user()?->id;
        });
    }

    protected function casts(): array
    {
        return [
            'category' => TicketCategory::class,
            'priority' => TicketPriority::class,
            'status' => TicketStatus::class,
        ];
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class, 'server_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class);
    }

    public function close(?string $answer = null): void
    {
        if ($answer) {
            $this->messages()->create([
                'message' => $answer,
                'author_id' => auth()->user()?->id,
            ]);
        }

        $this->status = TicketStatus::Closed;

        $this->save();

        // Send notification to author if existing and is the owner or a subuser of the server
        if ($this->author && collect($this->author->directAccessibleServers()->pluck('id')->all())->contains($this->server->id)) {
            Notification::make()
                ->title(trans('tickets::tickets.notifications.closed'))
                ->body($answer ? Markdown::inline($answer) : null)
                ->actions([
                    Action::make('view')
                        ->label(trans(('filament-actions::view.single.label')))
                        ->button()
                        ->markAsRead()
                        ->url(fn () => ListTickets::getUrl([
                            'tab' => 'closed',
                            'tableAction' => 'view',
                            'tableActionRecord' => $this->id,
                        ], panel: 'server', tenant: $this->server)),
                ])
                ->sendToDatabase($this->author);
        }
    }

    public function assignTo(User $user, bool $setStatus = true): void
    {
        $this->assigned_user_id = $user->id;

        if ($setStatus) {
            $this->status = TicketStatus::InProgress;
        }

        $this->save();
    }
}
