<?php

namespace Boy132\Tickets\Filament\Server\Resources\Tickets\Pages;

use Boy132\Tickets\Filament\Server\Resources\Tickets\TicketResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    protected static bool $canCreateAnother = false;

    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('create')
                ->label(__('filament-panels::resources/pages/create-record.form.actions.create.label'))
                ->submit('create')
                ->color('primary')
                ->button(),
            
            Action::make('cancel')
                ->label(__('filament-panels::resources/pages/create-record.form.actions.cancel.label'))
                ->url($this->getResource()::getUrl())
                ->color('gray')
                ->button(),
        ];
    }
}
