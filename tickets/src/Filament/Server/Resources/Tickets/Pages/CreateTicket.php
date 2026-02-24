<?php

namespace Boy132\Tickets\Filament\Server\Resources\Tickets\Pages;

use Boy132\Tickets\Filament\Server\Resources\Tickets\TicketResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    protected static bool $canCreateAnother = false;

    protected function getHeaderActions(): array
    {
        return [
            $this->getCreateFormAction()->formId('form')
                ->tooltip(fn (Action $action) => $action->getLabel())
                ->hiddenLabel()
                ->icon('tabler-plus'),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }
}
