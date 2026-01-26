<?php

namespace Boy132\Billing\Providers;

use Boy132\Billing\Http\Controllers\Api\CheckoutController;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Route;

class BillingRoutesProvider extends RouteServiceProvider
{
    public function boot(): void
    {
        $this->routes(function () {
            Route::prefix('checkout')->withoutMiddleware(['auth'])->group(function () {
                Route::get('/success', [CheckoutController::class, 'success'])->name('billing.checkout.success');
                Route::get('/cancel', [CheckoutController::class, 'cancel'])->name('billing.checkout.cancel');
            });
        });
    }
}
