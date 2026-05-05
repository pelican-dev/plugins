<?php

namespace Boy132\Billing\Filament\Admin\Resources\Customers\Pages;

use Boy132\Billing\Filament\Admin\Resources\Customers\CustomerResource;
use Filament\Resources\Pages\ListRecords;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;
}
