<?php

namespace Boy132\Billing\Http\Controllers\Api;

use App\Filament\Server\Pages\Console;
use App\Http\Controllers\Controller;
use Boy132\Billing\Filament\App\Resources\Orders\Pages\ListOrders;
use Boy132\Billing\Models\Order;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Stripe\Checkout\Session;
use Stripe\StripeClient;

class CheckoutController extends Controller
{
    public function __construct(
        private StripeClient $stripeClient
    ) {}

    public function success(Request $request): Redirector|RedirectResponse
    {
        $sessionId = $request->get('session_id');

        if ($sessionId === null) {
            return redirect(Filament::getPanel('app')->getUrl());
        }

        $session = $this->stripeClient->checkout->sessions->retrieve($sessionId);

        if ($session->payment_status === Session::PAYMENT_STATUS_UNPAID) {
            return redirect(ListOrders::getUrl(panel: 'app'));
        }

        /** @var ?Order $order */
        $order = Order::where('stripe_checkout_id', $session->id)->first();

        if (!$order) {
            return redirect(ListOrders::getUrl(panel: 'app'));
        }

        $order->activate($session->payment_intent);
        $order->refresh();

        return redirect(Console::getUrl(panel: 'server', tenant: $order->server));
    }

    public function cancel(Request $request): RedirectResponse
    {
        $sessionId = $request->get('session_id');

        if ($sessionId) {
            /** @var ?Order $order */
            $order = Order::where('stripe_checkout_id', $sessionId)->first();
            $order?->close();
        }

        return redirect(ListOrders::getUrl(panel: 'app'));
    }
}
