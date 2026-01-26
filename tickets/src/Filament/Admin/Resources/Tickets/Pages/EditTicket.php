<?php

namespace Boy132\Tickets\Filament\Admin\Resources\Tickets\Pages;

use Boy132\Tickets\Filament\Admin\Resources\Tickets\TicketResource;
use Boy132\Tickets\Filament\Components\Actions\AnswerAction;
use Boy132\Tickets\Filament\Components\Actions\AssignToMeAction;
use Filament\Resources\Pages\EditRecord;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            AnswerAction::make(),
            AssignToMeAction::make(),
            $this->getSaveFormAction()->formId('form'),
            $this->getCancelFormAction()->formId('form'),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
