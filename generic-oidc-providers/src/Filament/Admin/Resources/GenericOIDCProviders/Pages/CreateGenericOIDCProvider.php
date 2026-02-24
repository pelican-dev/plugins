<?php

namespace Boy132\GenericOIDCProviders\Filament\Admin\Resources\GenericOIDCProviders\Pages;

use Boy132\GenericOIDCProviders\Filament\Admin\Resources\GenericOIDCProviders\GenericOIDCProviderResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateGenericOIDCProvider extends CreateRecord
{
    protected static string $resource = GenericOIDCProviderResource::class;

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

    protected function getFormActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['id'] = Str::slug($data['id'], '_');

        return $data;
    }
}
