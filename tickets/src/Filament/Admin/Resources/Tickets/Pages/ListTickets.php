<?php

namespace Boy132\Tickets\Filament\Admin\Resources\Tickets\Pages;

use Boy132\Tickets\Enums\TicketStatus;
use Boy132\Tickets\Filament\Admin\Resources\Tickets\TicketResource;
use Boy132\Tickets\Models\Ticket;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    public function getTabs(): array
    {
        return [
            'my' => Tab::make(trans('tickets::tickets.assigned_to_me'))
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNot('status', TicketStatus::Closed->value)->where('assigned_user_id', auth()->user()->id))
                ->badge(fn () => Ticket::whereNot('status', TicketStatus::Closed->value)->where('assigned_user_id', auth()->user()->id)->count()),

            'open' => Tab::make(trans('tickets::tickets.open'))
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNot('status', TicketStatus::Closed->value))
                ->badge(fn () => Ticket::whereNot('status', TicketStatus::Closed->value)->count()),

            'closed' => Tab::make(trans('tickets::tickets.closed'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', TicketStatus::Closed->value))
                ->badge(fn () => Ticket::where('status', TicketStatus::Closed->value)->count()),

            'all' => Tab::make(trans('tickets::tickets.all'))
                ->badge(fn () => Ticket::count()),
        ];
    }
}
