<?php

namespace Boy132\Billing\Console\Commands;

use Boy132\Billing\Enums\OrderStatus;
use Boy132\Billing\Filament\App\Resources\Orders\Pages\ListOrders;
use Boy132\Billing\Models\Order;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Console\Command;

class CheckOrdersCommand extends Command
{
    protected $signature = 'p:billing:check-orders';

    protected $description = 'Checks the expire date for orders.';

    public function handle(): int
    {
        $orders = Order::all();

        if ($orders->count() < 1) {
            $this->line('No orders');

            return 0;
        }

        $bar = $this->output->createProgressBar($orders->count());
        foreach ($orders as $order) {
            $bar->clear();

            if ($order->checkExpire()) {
                Notification::make()
                    ->success()
                    ->title('Order expired')
                    ->body($order->getLabel())
                    ->actions([
                        Action::make('goto_orders')
                            ->label('Go to orders')
                            ->markAsRead()
                            ->url(ListOrders::getUrl(panel: 'app')),
                        Action::make('renew')
                            ->visible(fn (Order $order) => $order->status === OrderStatus::Expired && $order->productPrice->renewable)
                            ->label('Renew')
                            ->color('warning')
                            ->requiresConfirmation()
                            ->markAsRead()
                            ->action(fn (Order $order) => redirect($order->getCheckoutSession()->url)),
                    ])
                    ->sendToDatabase($order->customer->user);
            }

            $bar->advance();
            $bar->display();
        }

        $this->line('');

        return 0;
    }
}
