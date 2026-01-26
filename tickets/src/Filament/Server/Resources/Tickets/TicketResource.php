<?php

namespace Boy132\Tickets\Filament\Server\Resources\Tickets;

use App\Filament\Admin\Resources\Users\Pages\EditUser;
use App\Filament\Components\Tables\Columns\DateTimeColumn;
use Boy132\Tickets\Enums\TicketCategory;
use Boy132\Tickets\Enums\TicketPriority;
use Boy132\Tickets\Enums\TicketStatus;
use Boy132\Tickets\Filament\Server\Resources\Tickets\Pages\CreateTicket;
use Boy132\Tickets\Filament\Server\Resources\Tickets\Pages\ListTickets;
use Boy132\Tickets\Filament\Server\Resources\Tickets\Pages\ViewTicket;
use Boy132\Tickets\Filament\Server\Resources\Tickets\RelationManagers\MessagesRelationManager;
use Boy132\Tickets\Models\Ticket;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Markdown;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-ticket';

    protected static ?int $navigationSort = 20;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationLabel(): string
    {
        return trans_choice('tickets::tickets.ticket', 2);
    }

    public static function getModelLabel(): string
    {
        return trans_choice('tickets::tickets.ticket', 1);
    }

    public static function getPluralModelLabel(): string
    {
        return trans_choice('tickets::tickets.ticket', 2);
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Ticket::whereNot('status', TicketStatus::Closed->value)->count();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('title')
                    ->label(trans_choice('tickets::tickets.title', 1))
                    ->description(fn (Ticket $ticket) => Markdown::inline($ticket->description ?? ''))
                    ->sortable()
                    ->searchable()
                    ->grow(),
                TextColumn::make('category')
                    ->label(trans('tickets::tickets.category'))
                    ->badge()
                    ->toggleable(),
                TextColumn::make('priority')
                    ->label(trans('tickets::tickets.priority'))
                    ->badge()
                    ->toggleable(),
                TextColumn::make('status')
                    ->label(trans('tickets::tickets.status'))
                    ->badge()
                    ->toggleable(),
                TextColumn::make('assignedUser.username')
                    ->label(trans('tickets::tickets.assigned_to'))
                    ->icon('tabler-user')
                    ->placeholder(trans('tickets::tickets.noone'))
                    ->url(fn (Ticket $ticket) => $ticket->assignedUser && auth()->user()->can('update user', $ticket->assignedUser) ? EditUser::getUrl(['record' => $ticket->assignedUser], panel: 'admin') : null)
                    ->toggleable(),
                TextColumn::make('author.username')
                    ->label(trans('tickets::tickets.created_by'))
                    ->icon('tabler-user')
                    ->placeholder(trans('tickets::tickets.unknown'))
                    ->url(fn (Ticket $ticket) => $ticket->author && auth()->user()->can('update user', $ticket->author) ? EditUser::getUrl(['record' => $ticket->author], panel: 'admin') : null)
                    ->toggleable(),
                DateTimeColumn::make('created_at')
                    ->label(trans('tickets::tickets.created_at'))
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->groups([
                Group::make('category')
                    ->label(trans('tickets::tickets.category')),
                Group::make('priority')
                    ->label(trans('tickets::tickets.priority')),
                Group::make('status')
                    ->label(trans('tickets::tickets.status')),
                Group::make('author.username')
                    ->label(trans('tickets::tickets.created_by')),
            ])
            ->emptyStateIcon('tabler-ticket')
            ->emptyStateDescription('')
            ->emptyStateHeading(trans('tickets::tickets.no_tickets'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label(trans_choice('tickets::tickets.title', 1))
                    ->required()
                    ->columnSpanFull(),
                Select::make('category')
                    ->label(trans('tickets::tickets.category'))
                    ->required()
                    ->options(TicketCategory::class),
                Select::make('priority')
                    ->label(trans('tickets::tickets.priority'))
                    ->required()
                    ->options(TicketPriority::class)
                    ->default(TicketPriority::Normal),
                MarkdownEditor::make('description')
                    ->label(trans('tickets::tickets.description'))
                    ->columnSpanFull(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columnSpanFull()
                    ->columns(['default' => 1, 'md' => 2, 'lg' => 3])
                    ->schema([
                        TextEntry::make('title')
                            ->label(trans_choice('tickets::tickets.title', 1))
                            ->columnSpanFull(),
                        TextEntry::make('category')
                            ->label(trans('tickets::tickets.category'))
                            ->badge(),
                        TextEntry::make('priority')
                            ->label(trans('tickets::tickets.priority'))
                            ->badge(),
                        TextEntry::make('status')
                            ->label(trans('tickets::tickets.status'))
                            ->badge(),
                        TextEntry::make('assignedUser.username')
                            ->label(trans('tickets::tickets.assigned_to'))
                            ->icon('tabler-user')
                            ->placeholder(trans('tickets::tickets.noone'))
                            ->url(fn (Ticket $ticket) => $ticket->assignedUser && auth()->user()->can('update user', $ticket->assignedUser) ? EditUser::getUrl(['record' => $ticket->assignedUser], panel: 'admin') : null),
                        TextEntry::make('author.username')
                            ->label(trans('tickets::tickets.created_by'))
                            ->icon('tabler-user')
                            ->placeholder(trans('tickets::tickets.unknown'))
                            ->url(fn (Ticket $ticket) => $ticket->author && auth()->user()->can('update user', $ticket->author) ? EditUser::getUrl(['record' => $ticket->author], panel: 'admin') : null),
                        TextEntry::make('created_at')
                            ->label(trans('tickets::tickets.created_at'))
                            ->since(timezone: auth()->user()->timezone ?? config('app.timezone', 'UTC'))
                            ->dateTimeTooltip(timezone: auth()->user()->timezone ?? config('app.timezone', 'UTC')),
                    ]),
                Section::make(trans('tickets::tickets.description'))
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('description')
                            ->hiddenLabel()
                            ->markdown()
                            ->placeholder(trans('tickets::tickets.no_description')),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTickets::route('/'),
            'create' => CreateTicket::route('/create'),
            'view' => ViewTicket::route('/{record}'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            MessagesRelationManager::class,
        ];
    }

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function getViewAnyAuthorizationResponse(): Response
    {
        return Response::allow();
    }

    public static function canView(Model $record): bool
    {
        return true;
    }

    public static function getViewAuthorizationResponse(Model $record): Response
    {
        return Response::allow();
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function getCreateAuthorizationResponse(): Response
    {
        return Response::allow();
    }
}
