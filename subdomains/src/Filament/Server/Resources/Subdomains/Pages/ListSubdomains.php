<?php

namespace Boy132\Subdomains\Filament\Server\Resources\Subdomains\Pages;

use Boy132\Subdomains\Filament\Server\Resources\Subdomains\SubdomainResource;
use Filament\Resources\Pages\ListRecords;

class ListSubdomains extends ListRecords
{
    protected static string $resource = SubdomainResource::class;

    public function getBreadcrumbs(): array
    {
        return [];
    }
}
