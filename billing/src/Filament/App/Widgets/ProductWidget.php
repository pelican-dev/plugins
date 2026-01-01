<?php

namespace Boy132\Billing\Filament\App\Widgets;

use Boy132\Billing\Models\Customer;
use Boy132\Billing\Models\Order;
use Boy132\Billing\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Widgets\Widget;
use Illuminate\Support\Number;

class ProductWidget extends Widget implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    protected string $view = 'billing::widget'; // @phpstan-ignore property.defaultValue

    public ?Product $product = null;

    public function content(Schema $schema): Schema
    {
        $actions = [];

        foreach ($this->product->prices as $price) {
            $actions[] = Action::make(str_slug($price->name))
                ->label($price->getLabel())
                ->action(function () use ($price) {
                    $price->sync();

                    /** @var Customer $customer */
                    $customer = Customer::firstOrCreate([
                        'user_id' => user()->id,
                    ]);

                    /** @var Order $order */
                    $order = Order::create([
                        'customer_id' => $customer->id,
                        'product_price_id' => $price->id,
                    ]);

                    return $this->redirect($order->getCheckoutSession()->url);
                });
        }

        return $schema
            ->record($this->product)
            ->components([
                Section::make()
                    ->heading($this->product->getLabel())
                    ->description($this->product->description)
                    ->columns(6)
                    ->schema([
                        TextEntry::make('cpu')
                            ->label('CPU')
                            ->icon('tabler-cpu')
                            ->formatStateUsing(fn ($state) => $state === 0 ? 'Unlimited' : $state . ' %')
                            ->columnSpan(2),
                        TextEntry::make('memory')
                            ->icon('tabler-database')
                            ->formatStateUsing(fn ($state) => $state === 0 ? 'Unlimited' : Number::format($state / (config('panel.use_binary_prefix') ? 1024 : 1000), 2, locale: auth()->user()->language) . (config('panel.use_binary_prefix') ? ' GiB' : ' GB'))
                            ->columnSpan(2),
                        TextEntry::make('disk')
                            ->icon('tabler-folder')
                            ->formatStateUsing(fn ($state) => $state === 0 ? 'Unlimited' : Number::format($state / (config('panel.use_binary_prefix') ? 1024 : 1000), 2, locale: auth()->user()->language) . (config('panel.use_binary_prefix') ? ' GiB' : ' GB'))
                            ->columnSpan(2),
                        TextEntry::make('backup_limit')
                            ->inlineLabel()
                            ->columnSpan(3)
                            ->visible(fn ($state) => $state > 0),
                        TextEntry::make('database_limit')
                            ->inlineLabel()
                            ->columnSpan(3)
                            ->visible(fn ($state) => $state > 0),
                        Actions::make($actions)
                            ->columnSpanFull()
                            ->fullWidth(),
                    ]),
            ]);
    }
}
