<?php

namespace Boy132\Billing\Filament\Admin\Resources\Orders\Pages;

use Boy132\Billing\Enums\OrderStatus;
use Boy132\Billing\Filament\Admin\Resources\Orders\OrderResource;
use Boy132\Billing\Models\Order;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    public function getDefaultActiveTab(): string
    {
        return OrderStatus::Active->value;
    }

    public function getTabs(): array
    {
        $tabs = [];

        foreach (OrderStatus::cases() as $orderStatus) {
            $tabs[$orderStatus->value] = Tab::make($orderStatus->getLabel())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', $orderStatus->value))
                ->badge(fn () => Order::where('status', $orderStatus->value)->count())
                ->icon(fn () => $orderStatus->getIcon());
        }

        $tabs['all'] = Tab::make('All')->badge(fn () => Order::count());

        return $tabs;
    }
}
