<?php

namespace Boy132\Billing\Models;

use App\Enums\SuspendAction;
use App\Models\Objects\DeploymentObject;
use App\Models\Server;
use App\Services\Servers\ServerCreationService;
use App\Services\Servers\SuspensionService;
use Boy132\Billing\Enums\OrderStatus;
use Boy132\Billing\Enums\PriceInterval;
use Exception;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Stripe\Checkout\Session;
use Stripe\StripeClient;

/**
 * @property int $id
 * @property ?string $stripe_checkout_id
 * @property ?string $stripe_payment_id
 * @property OrderStatus $status
 * @property ?Carbon $expires_at
 * @property int $customer_id
 * @property Customer $customer
 * @property int $product_price_id
 * @property ProductPrice $productPrice
 * @property ?int $server_id
 * @property ?Server $server
 */
class Order extends Model implements HasLabel
{
    protected $fillable = [
        'stripe_checkout_id',
        'stripe_payment_id',
        'status',
        'expires_at',
        'customer_id',
        'product_price_id',
        'server_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'expires_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->BelongsTo(Customer::class, 'customer_id');
    }

    public function productPrice(): BelongsTo
    {
        return $this->BelongsTo(ProductPrice::class, 'product_price_id');
    }

    public function server(): BelongsTo
    {
        return $this->BelongsTo(Server::class, 'server_id');
    }

    public function getLabel(): string
    {
        return "Order #{$this->id}";
    }

    public function checkExpire(): bool
    {
        if ($this->status === OrderStatus::Active && !is_null($this->expires_at) && now('UTC') >= $this->expires_at) {
            try {
                if ($this->server) {
                    app(SuspensionService::class)->handle($this->server, SuspendAction::Suspend);
                }
            } catch (Exception $exception) {
                report($exception);
            }

            $this->expireCheckoutSession();

            $this->update([
                'stripe_checkout_id' => null,
                'status' => OrderStatus::Expired,
            ]);

            return true;
        }

        return false;
    }

    private function expireCheckoutSession(): void
    {
        if (!is_null($this->stripe_checkout_id)) {
            /** @var StripeClient $stripeClient */
            $stripeClient = app(StripeClient::class);

            $session = $stripeClient->checkout->sessions->retrieve($this->stripe_checkout_id);

            if ($session->status === Session::STATUS_OPEN) {
                $stripeClient->checkout->sessions->expire($session->id);
            }
        }
    }

    public function getCheckoutSession(): Session
    {
        /** @var StripeClient $stripeClient */
        $stripeClient = app(StripeClient::class);

        if (is_null($this->stripe_checkout_id)) {
            $session = $stripeClient->checkout->sessions->create([
                'customer_email' => $this->customer->user->email,
                'success_url' => route('billing.checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('billing.checkout.cancel') . '?session_id={CHECKOUT_SESSION_ID}',
                'line_items' => [
                    [
                        'price' => $this->productPrice->stripe_id,
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                'allow_promotion_codes' => true,
                'branding_settings' => [
                    'display_name' => config('app.name'),
                    'logo' => [
                        'type' => 'url',
                        'url' => asset(config('app.logo') ?? 'pelican.svg'),
                    ],
                ],
            ]);

            $this->update([
                'stripe_checkout_id' => $session->id,
            ]);

            return $session;
        }

        return $stripeClient->checkout->sessions->retrieve($this->stripe_checkout_id);
    }

    public function activate(?string $stripePaymentId): void
    {
        $expireDate = match ($this->productPrice->interval_type) {
            PriceInterval::Day => now('UTC')->addDays($this->productPrice->interval_value),
            PriceInterval::Week => now('UTC')->addWeeks($this->productPrice->interval_value),
            PriceInterval::Month => now('UTC')->addMonths($this->productPrice->interval_value),
            PriceInterval::Year => now('UTC')->addYears($this->productPrice->interval_value),
        };

        $this->expireCheckoutSession();

        $this->update([
            'stripe_checkout_id' => null,
            'stripe_payment_id' => $stripePaymentId,
            'status' => OrderStatus::Active,
            'expires_at' => $expireDate,
        ]);

        try {
            if ($this->server) {
                app(SuspensionService::class)->handle($this->server, SuspendAction::Unsuspend);
            } else {
                $this->createServer();
            }
        } catch (Exception $exception) {
            report($exception);
        }
    }

    public function close(): void
    {
        try {
            if ($this->server) {
                app(SuspensionService::class)->handle($this->server, SuspendAction::Suspend);
            }
        } catch (Exception $exception) {
            report($exception);
        }

        $this->expireCheckoutSession();

        $this->update([
            'stripe_checkout_id' => null,
            'status' => OrderStatus::Closed,
        ]);
    }

    public function createServer(): Server
    {
        if ($this->server) {
            return $this->server;
        }

        $product = $this->productPrice->product;

        $environment = [];
        foreach ($product->egg->variables as $variable) {
            $environment[$variable->env_variable] = $variable->default_value;
        }

        $data = [
            'name' => $this->getLabel() . ' (' . $this->productPrice->product->getLabel() . ')',
            'owner_id' => $this->customer->user->id,
            'egg_id' => $product->egg->id,
            'cpu' => $product->cpu,
            'memory' => $product->memory,
            'disk' => $product->disk,
            'swap' => $product->swap,
            'io' => 500,
            'environment' => $environment,
            'skip_scripts' => false,
            'start_on_completion' => true,
            'oom_killer' => false,
            'database_limit' => $product->database_limit,
            'allocation_limit' => $product->allocation_limit,
            'backup_limit' => $product->backup_limit,
        ];

        $object = new DeploymentObject();
        $object->setDedicated(false);
        $object->setTags($product->tags);
        $object->setPorts($product->ports);

        $server = app(ServerCreationService::class)->handle($data, $object);

        $this->update([
            'server_id' => $server->id,
        ]);

        return $server;
    }
}
