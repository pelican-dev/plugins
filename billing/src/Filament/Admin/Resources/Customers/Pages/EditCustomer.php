<?php

namespace Boy132\Billing\Filament\Admin\Resources\Customers\Pages;

use Boy132\Billing\Filament\Admin\Resources\Customers\CustomerResource;
use Boy132\Billing\Filament\Admin\Resources\Customers\RelationManagers\OrderRelationManager;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getFormActions(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->getCancelFormAction()->formId('form')
                ->tooltip(fn (Action $action) => $action->getLabel())
                ->hiddenLabel()
                ->icon('tabler-arrow-left'),
            DeleteAction::make(),
            $this->getSaveFormAction()->formId('form')
                ->tooltip(fn (Action $action) => $action->getLabel())
                ->hiddenLabel()
                ->icon('tabler-device-floppy'),
        ];
    }

    public function getRelationManagers(): array
    {
        return [
            OrderRelationManager::class,
        ];
    }
}
