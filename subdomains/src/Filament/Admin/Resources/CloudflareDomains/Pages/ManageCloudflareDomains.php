<?php

namespace Boy132\Subdomains\Filament\Admin\Resources\CloudflareDomains\Pages;

use Boy132\Subdomains\Filament\Admin\Resources\CloudflareDomains\CloudflareDomainResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCloudflareDomains extends ManageRecords
{
    protected static string $resource = CloudflareDomainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->createAnother(false)
                ->successNotification(null),
        ];
    }
}
