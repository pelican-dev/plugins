<?php

namespace Boy132\Billing\Providers;

use App\Models\Role;
use Boy132\Billing\Console\Commands\CheckOrdersCommand;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\ServiceProvider;
use Stripe\StripeClient;

class BillingPluginProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(StripeClient::class, fn () => new StripeClient(config('billing.secret')));

        Role::registerCustomDefaultPermissions('customer');
        Role::registerCustomModelIcon('customer', 'tabler-user-dollar');

        Role::registerCustomDefaultPermissions('product');
        Role::registerCustomModelIcon('product', 'tabler-package');
    }

    public function boot(): void
    {
        Schedule::command(CheckOrdersCommand::class)->everyMinute()->withoutOverlapping();
    }
}
