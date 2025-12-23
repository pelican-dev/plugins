<?php

namespace Boy132\Tickets\Filament\Server\Resources\Tickets;

use App\Filament\Admin\Resources\Users\Pages\EditUser;
use App\Filament\Components\Tables\Columns\DateTimeColumn;
use Boy132\Tickets\Enums\TicketCategory;
use Boy132\Tickets\Enums\TicketPriority;
use Boy132\Tickets\Filament\Server\Resources\Tickets\Pages\ManageTickets;
use Boy132\Tickets\Models\Ticket;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
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
                TextColumn::make('author.username')
                    ->label(trans('tickets::tickets.created_by'))
                    ->icon('tabler-user')
                    ->placeholder(trans('tickets::tickets.unknown'))
                    ->url(fn (Ticket $ticket) => $ticket->author && auth()->user()->can('update user', $ticket->author) ? EditUser::getUrl(['record' => $ticket->author], panel: 'admin') : null)
                    ->toggleable(),
                DateTimeColumn::make('created_at')
                    ->label(trans('tickets::tickets.created_at'))
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
                TextEntry::make('title')
                    ->label(trans_choice('tickets::tickets.title', 1))
                    ->columnSpanFull(),
                TextEntry::make('category')
                    ->label(trans('tickets::tickets.category'))
                    ->badge(),
                TextEntry::make('priority')
                    ->label(trans('tickets::tickets.priority'))
                    ->badge(),
                TextEntry::make('description')
                    ->label(trans('tickets::tickets.description'))
                    ->columnSpanFull()
                    ->markdown()
                    ->placeholder(trans('tickets::tickets.no_description')),
                TextEntry::make('answer')
                    ->visible(fn (Ticket $ticket) => $ticket->is_answered)
                    ->label(trans('tickets::tickets.answer_noun'))
                    ->columnSpanFull()
                    ->markdown(),
                TextEntry::make('author.username')
                    ->label(trans('tickets::tickets.created_by'))
                    ->icon('tabler-user')
                    ->placeholder(trans('tickets::tickets.unknown'))
                    ->url(fn (Ticket $ticket) => $ticket->author && auth()->user()->can('update user', $ticket->author) ? EditUser::getUrl(['record' => $ticket->author], panel: 'admin') : null),
                TextEntry::make('created_at')
                    ->label(trans('tickets::tickets.created_at'))
                    ->dateTime(timezone: auth()->user()->timezone ?? config('app.timezone', 'UTC')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTickets::route('/'),
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
