<?php

namespace Boy132\Billing\Filament\Admin\Resources\Coupons\Pages;

use Boy132\Billing\Enums\CouponType;
use Boy132\Billing\Filament\Admin\Resources\Coupons\CouponResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCoupon extends EditRecord
{
    protected static string $resource = CouponResource::class;

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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['coupon_type'] = $data['amount_off'] ? CouponType::Amount : CouponType::Percentage;

        return $data;
    }
}
