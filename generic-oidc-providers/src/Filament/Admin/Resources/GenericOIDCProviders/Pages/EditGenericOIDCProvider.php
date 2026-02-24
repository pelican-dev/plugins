<?php

namespace Boy132\GenericOIDCProviders\Filament\Admin\Resources\GenericOIDCProviders\Pages;

use Boy132\GenericOIDCProviders\Filament\Admin\Resources\GenericOIDCProviders\GenericOIDCProviderResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGenericOIDCProvider extends EditRecord
{
    protected static string $resource = GenericOIDCProviderResource::class;

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
}
