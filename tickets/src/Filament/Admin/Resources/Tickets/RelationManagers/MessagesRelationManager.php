<?php

namespace Boy132\Tickets\Filament\Admin\Resources\Tickets\RelationManagers;

use App\Filament\Admin\Resources\Users\Pages\EditUser;
use App\Filament\Components\Tables\Columns\DateTimeColumn;
use Boy132\Tickets\Models\Ticket;
use Boy132\Tickets\Models\TicketMessage;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * @method Ticket getOwnerRecord()
 */
class MessagesRelationManager extends RelationManager
{
    protected static string $relationship = 'messages';

    public function table(Table $table): Table
    {
        return $table
            ->modelLabel(trans_choice('tickets::tickets.message', 1))
            ->pluralModelLabel(trans_choice('tickets::tickets.message', 2))
            ->paginated(false)
            ->defaultSort('created_at', 'desc')
            ->columns([
                Stack::make([
                    TextColumn::make('message')
                        ->markdown(),
                    Split::make([
                        TextColumn::make('author.username')
                            ->grow(false)
                            ->placeholder(trans('tickets::tickets.unknown'))
                            ->icon('tabler-user')
                            ->url(fn (TicketMessage $ticketMessage) => $ticketMessage->author && auth()->user()->can('edit user', $ticketMessage->author) ? EditUser::getUrl(['record' => $ticketMessage->author], panel: 'admin') : null),
                        DateTimeColumn::make('created_at')
                            ->grow(false)
                            ->since(),
                        TextColumn::make('is_hidden')
                            ->grow(false)
                            ->badge()
                            ->color('warning')
                            ->state(fn (TicketMessage $ticketMessage) => $ticketMessage->hidden ? trans('tickets::tickets.hidden') : null),
                        TextColumn::make('is_author')
                            ->grow(false)
                            ->badge()
                            ->color('success')
                            ->state(fn (TicketMessage $ticketMessage) => $ticketMessage->author_id === $this->getOwnerRecord()->author_id ? trans('tickets::tickets.author') : null),
                        TextColumn::make('is_assigned')
                            ->grow(false)
                            ->badge()
                            ->state(fn (TicketMessage $ticketMessage) => $ticketMessage->author_id === $this->getOwnerRecord()->assigned_user_id ? trans('tickets::tickets.admin') : null),
                    ]),
                ])->space(3),
            ])
            ->headerActions([
                CreateAction::make()
                    ->createAnother(false)
                    ->schema([
                        MarkdownEditor::make('message')
                            ->label(trans_choice('tickets::tickets.message', 1))
                            ->required(),
                        Toggle::make('hidden')
                            ->label(trans('tickets::tickets.hidden') . '?'),
                    ]),
            ]);
    }
}
