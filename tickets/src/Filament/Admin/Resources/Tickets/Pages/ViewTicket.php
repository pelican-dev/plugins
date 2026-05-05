<?php

namespace Boy132\Tickets\Filament\Admin\Resources\Tickets\Pages;

use Boy132\Tickets\Filament\Admin\Resources\Tickets\TicketResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
