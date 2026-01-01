<?php

namespace Boy132\Billing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Stripe\StripeClient;

/**
 * @property int $id
 * @property ?string $stripe_coupon_id
 * @property ?string $stripe_promotion_id
 * @property string $name
 * @property string $code
 * @property ?int $amount_off
 * @property ?int $percent_off
 * @property ?int $max_redemptions
 * @property ?Carbon $redeem_by
 */
class Coupon extends Model
{
    protected $fillable = [
        'stripe_coupon_id',
        'stripe_promotion_id',
        'name',
        'code',
        'amount_off',
        'percent_off',
        'max_redemptions',
        'redeem_by',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model) {
            $model->code ??= Str::random(8);
        });

        static::created(function (self $model) {
            $model->sync();
        });

        static::updating(function (self $model) {
            $model->code ??= Str::random(8);
        });

        static::updated(function (self $model) {
            $model->sync();
        });

        static::deleted(function (self $model) {
            if (!is_null($model->stripe_coupon_id)) {
                /** @var StripeClient $stripeClient */
                $stripeClient = app(StripeClient::class);

                $stripeClient->coupons->delete($model->stripe_coupon_id);
                $stripeClient->coupons->delete($model->stripe_promotion_id);
            }
        });
    }

    public function sync(): void
    {
        /** @var StripeClient $stripeClient */
        $stripeClient = app(StripeClient::class);

        if (is_null($this->stripe_coupon_id)) {
            $data = [
                'name' => $this->name,
            ];

            if ($this->amount_off) {
                $data['currency'] = config('billing.currency');
                $data['amount_off'] = $this->amount_off;
            }

            if ($this->percent_off) {
                $data['percent_off'] = $this->percent_off;
            }

            if ($this->max_redemptions) {
                $data['max_redemptions'] = $this->max_redemptions;
            }

            if ($this->redeem_by) {
                $data['redeem_by'] = $this->redeem_by->timestamp;
            }

            $stripeCoupon = $stripeClient->coupons->create($data);

            $stripePromotionCode = $stripeClient->promotionCodes->create([
                'code' => $this->code,
                'promotion' => [
                    'type' => 'coupon',
                    'coupon' => $stripeCoupon->id,
                ],
            ]);

            $this->updateQuietly([
                'stripe_coupon_id' => $stripeCoupon->id,
                'stripe_promotion_id' => $stripePromotionCode->id,
            ]);
        } else {
            $stripeCoupon = $stripeClient->coupons->retrieve($this->stripe_coupon_id);

            // You can't update coupon objects on stripe, so check for changes and recreate the coupon if needed
            if ($stripeCoupon->amount_off !== $this->amount_off || $stripeCoupon->percent_off !== $this->percent_off || $stripeCoupon->max_redemptions !== $this->max_redemptions || $stripeCoupon->redeem_by !== $this->redeem_by) {
                $stripeClient->coupons->delete($this->stripe_coupon_id);
                $stripeClient->coupons->delete($this->stripe_promotion_id);

                $this->stripe_coupon_id = null;
                $this->sync();
            }
        }
    }
}
