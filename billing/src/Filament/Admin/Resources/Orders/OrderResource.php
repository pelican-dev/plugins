<?php

namespace Boy132\Billing\Filament\Admin\Resources\Orders;

use App\Filament\Admin\Resources\Servers\Pages\EditServer;
use App\Filament\Components\Tables\Columns\DateTimeColumn;
use Boy132\Billing\Enums\OrderStatus;
use Boy132\Billing\Filament\Admin\Resources\Customers\Pages\EditCustomer;
use Boy132\Billing\Filament\Admin\Resources\Orders\Pages\ListOrders;
use Boy132\Billing\Filament\Admin\Resources\Products\Pages\EditProduct;
use Boy132\Billing\Models\Customer;
use Boy132\Billing\Models\Order;
use Boy132\Billing\Models\ProductPrice;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use NumberFormatter;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-truck-delivery';

    protected static string|\UnitEnum|null $navigationGroup = 'Billing';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->count() ?: null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->label('Customer')
                    ->required()
                    ->selectablePlaceholder(false)
                    ->relationship('customer')
                    ->getOptionLabelFromRecordUsing(fn (Customer $customer) => $customer->getLabel())
                    ->preload(),
                Select::make('product_price_id')
                    ->label('Product')
                    ->required()
                    ->selectablePlaceholder(false)
                    ->relationship('productPrice')
                    ->getOptionLabelFromRecordUsing(fn (ProductPrice $productPrice) => $productPrice->product->getLabel() . ' (' . $productPrice->getLabel() . ')')
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('status')
                    ->sortable()
                    ->badge()
                    ->visible(fn ($livewire) => $livewire->activeTab === 'all'),
                TextColumn::make('customer')
                    ->label('Customer')
                    ->icon('tabler-user-dollar')
                    ->sortable()
                    ->url(fn (Order $order) => EditCustomer::getUrl(['record' => $order->customer])),
                TextColumn::make('server.name')
                    ->label('Server')
                    ->placeholder('No server')
                    ->icon('tabler-brand-docker')
                    ->sortable()
                    ->url(fn (Order $order) => $order->server ? EditServer::getUrl(['record' => $order->server]) : null),
                TextColumn::make('productPrice.product.name')
                    ->label('Product')
                    ->icon('tabler-package')
                    ->sortable()
                    ->url(fn (Order $order) => EditProduct::getUrl(['record' => $order->productPrice->product])),
                TextColumn::make('productPrice.name')
                    ->label('Price')
                    ->sortable(),
                TextColumn::make('productPrice.cost')
                    ->label('Cost')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        $formatter = new NumberFormatter(auth()->user()->language, NumberFormatter::CURRENCY);

                        return $formatter->formatCurrency($state, config('billing.currency'));
                    }),
                DateTimeColumn::make('expires_at')
                    ->label('Expires')
                    ->placeholder('No expire')
                    ->color(fn ($state) => $state <= now('UTC') ? 'danger' : null)
                    ->since(),
            ])
            ->recordActions([
                Action::make('activate')
                    ->visible(fn (Order $order) => $order->status !== OrderStatus::Active)
                    ->tooltip('Activate')
                    ->color('success')
                    ->icon('tabler-check')
                    ->requiresConfirmation()
                    ->action(function (Order $order) {
                        $order->activate(null);

                        Notification::make()
                            ->title('Order activated')
                            ->body($order->getLabel())
                            ->success()
                            ->send();
                    }),
                Action::make('create_server')
                    ->visible(fn (Order $order) => $order->status === OrderStatus::Active && !$order->server)
                    ->tooltip('Create server')
                    ->color('primary')
                    ->icon('tabler-brand-docker')
                    ->requiresConfirmation()
                    ->action(function (Order $order) {
                        try {
                            $order->createServer();
                        } catch (Exception $exception) {
                            Notification::make()
                                ->title('Could not create server')
                                ->body($exception->getMessage())
                                ->danger()
                                ->persistent()
                                ->send();
                        }
                    }),
                Action::make('close')
                    ->visible(fn (Order $order) => $order->status === OrderStatus::Active)
                    ->tooltip('Close')
                    ->color('danger')
                    ->icon('tabler-x')
                    ->requiresConfirmation()
                    ->action(function (Order $order) {
                        $order->close();

                        Notification::make()
                            ->title('Order closed')
                            ->body($order->getLabel())
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                CreateAction::make()
                    ->createAnother(false),
            ])
            ->emptyStateHeading('No Orders')
            ->emptyStateDescription('')
            ->emptyStateIcon('tabler-truck-delivery');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
        ];
    }
}
