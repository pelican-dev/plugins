<?php

namespace Boy132\Billing\Models;

use App\Models\Egg;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stripe\StripeClient;

/**
 * @property int $id
 * @property ?string $stripe_id
 * @property string $name
 * @property string $description
 * @property int $cpu
 * @property int $memory
 * @property int $disk
 * @property int $swap
 * @property array<int|string> $ports
 * @property string[] $tags
 * @property int $allocation_limit
 * @property int $database_limit
 * @property int $backup_limit
 * @property int $egg_id
 * @property Egg $egg
 * @property Collection|ProductPrice[] $prices
 */
class Product extends Model implements HasLabel
{
    protected $fillable = [
        'stripe_id',
        'name',
        'description',
        'egg_id',
        'cpu',
        'memory',
        'disk',
        'swap',
        'ports',
        'tags',
        'allocation_limit',
        'database_limit',
        'backup_limit',
    ];

    protected $attributes = [
        'ports' => '[]',
        'tags' => '[]',
    ];

    protected function casts(): array
    {
        return [
            'ports' => 'array',
            'tags' => 'array',
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

        static::deleted(function (self $model) {
            if (!is_null($model->stripe_id)) {
                /** @var StripeClient $stripeClient */
                $stripeClient = app(StripeClient::class);

                $stripeClient->products->delete($model->stripe_id);
            }
        });
    }

    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class, 'product_id');
    }

    public function egg(): BelongsTo
    {
        return $this->BelongsTo(Egg::class, 'egg_id');
    }

    public function getLabel(): string
    {
        return $this->name;
    }

    public function sync(): void
    {
        /** @var StripeClient $stripeClient */
        $stripeClient = app(StripeClient::class);

        if (is_null($this->stripe_id)) {
            $stripeProduct = $stripeClient->products->create([
                'name' => $this->name,
                'description' => $this->description,
            ]);

            $this->updateQuietly([
                'stripe_id' => $stripeProduct->id,
            ]);
        } else {
            $stripeClient->products->update($this->stripe_id, [
                'name' => $this->name,
                'description' => $this->description,
            ]);
        }
    }
}
