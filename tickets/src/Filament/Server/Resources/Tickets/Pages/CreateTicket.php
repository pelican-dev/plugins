<?php

namespace Boy132\Tickets\Filament\Server\Resources\Tickets\Pages;

use Boy132\Tickets\Filament\Server\Resources\Tickets\TicketResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    protected static bool $canCreateAnother = false;

    public function getBreadcrumbs(): array
    {
        return [];
    }
}
