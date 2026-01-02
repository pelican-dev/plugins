<?php

namespace Boy132\Billing\Models;

use Boy132\Billing\Enums\PriceInterval;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use NumberFormatter;
use Stripe\StripeClient;

/**
 * @property int $id
 * @property ?string $stripe_id
 * @property string $name
 * @property int $cost
 * @property PriceInterval $interval_type
 * @property int $interval_value
 * @property int $product_id
 * @property Product $product
 */
class ProductPrice extends Model implements HasLabel
{
    protected $fillable = [
        'stripe_id',
        'product_id',
        'name',
        'cost',
        'interval_type',
        'interval_value',
    ];

    protected function casts(): array
    {
        return [
            'interval_type' => PriceInterval::class,
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::created(function (self $model) {
            $model->sync();
        });

        static::updated(function (self $model) {
            $model->sync();
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getLabel(): string
    {
        return $this->interval_value . ' ' . str_plural($this->interval_type->getLabel(), $this->interval_value) . ' - ' . $this->formatCost();
    }

    public function sync(): void
    {
        $this->product->sync();

        /** @var StripeClient $stripeClient */
        $stripeClient = app(StripeClient::class);

        if (is_null($this->stripe_id)) {
            $stripePrice = $stripeClient->prices->create([
                'currency' => config('billing.currency'),
                'nickname' => $this->name,
                'product' => $this->product->stripe_id,
                'unit_amount' => $this->cost * 100, // Stripe needs cent/ penny price
            ]);

            $this->updateQuietly([
                'stripe_id' => $stripePrice->id,
            ]);
        } else {
            $stripePrice = $stripeClient->prices->retrieve($this->stripe_id);

            // You can't update price objects on stripe, so check for changes and recreate the price if needed
            if ($stripePrice->product !== $this->product->stripe_id || $stripePrice->unit_amount !== $this->cost * 100) {
                $this->stripe_id = null;
                $this->sync();
            }
        }
    }

    public function formatCost(): string
    {
        $formatter = new NumberFormatter(user()->language ?? 'en', NumberFormatter::CURRENCY);

        return $formatter->formatCurrency($this->cost, config('billing.currency'));
    }
}
