<?php

namespace Boy132\Billing\Filament\Admin\Resources\Coupons\Pages;

use Boy132\Billing\Filament\Admin\Resources\Coupons\CouponResource;
use Filament\Resources\Pages\ListRecords;

class ListCoupons extends ListRecords
{
    protected static string $resource = CouponResource::class;
}
