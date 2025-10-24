<?php

namespace Boy132\Tickets\Models;

use App\Models\Server;
use App\Models\User;
use Boy132\Tickets\Enums\TicketCategory;
use Boy132\Tickets\Enums\TicketPriority;
use Boy132\Tickets\Filament\Server\Resources\Tickets\Pages\ManageTickets;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Markdown;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $title
 * @property TicketCategory $category
 * @property TicketPriority $priority
 * @property ?string $description
 * @property bool $is_answered
 * @property ?string $answer
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
        'description',
        'is_answered',
        'answer',
        'server_id',
        'author_id',
        'assigned_user_id',
    ];

    protected function casts(): array
    {
        return [
            'category' => TicketCategory::class,
            'priority' => TicketPriority::class,
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

    public function answer(string $answer): void
    {
        $this->is_answered = true;
        $this->answer = $answer;

        $this->save();

        // Send notification to author if exisiting and is the owner or a subuser of the server
        if ($this->author && $this->author->directAccessibleServers()->where('id', $this->server->id)->exists()) {
            Notification::make()
                ->title(trans('tickets::tickets.notifications.answered'))
                ->body(Markdown::inline($this->answer))
                ->actions([
                    Action::make('view')
                        ->label(trans(('filament-actions::view.single.label')))
                        ->button()
                        ->markAsRead()
                        ->url(fn () => ManageTickets::getUrl([
                            'activeTab' => 'answered',
                            'tableAction' => 'view',
                            'tableActionRecord' => $this->id,
                        ], panel: 'server', tenant: $this->server)),
                ])
                ->sendToDatabase($this->author);
        }
    }

    public function assignTo(User $user): void
    {
        $this->assigned_user_id = $user->id;

        $this->save();
    }
}
