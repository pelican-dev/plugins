<?php

namespace Boy132\Billing\Filament\Admin\Resources\Coupons\Pages;

use Boy132\Billing\Filament\Admin\Resources\Coupons\CouponResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateCoupon extends CreateRecord
{
    protected static string $resource = CouponResource::class;

    protected static bool $canCreateAnother = false;

    protected function getFormActions(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->getCreateFormAction()->formId('form')
                ->tooltip(fn (Action $action) => $action->getLabel())
                ->hiddenLabel()
                ->icon('tabler-plus'),
        ];
    }
}
