<?php

namespace Boy132\Tickets\Filament\Server\Resources\Tickets\Pages;

use Boy132\Tickets\Filament\Server\Resources\Tickets\TicketResource;
use Filament\Resources\Pages\ViewRecord;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    public function getBreadcrumbs(): array
    {
        return [];
    }
}
