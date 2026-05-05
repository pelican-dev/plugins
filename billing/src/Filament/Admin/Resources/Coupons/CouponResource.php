<?php

namespace Boy132\Billing\Filament\Admin\Resources\Coupons;

use App\Filament\Components\Tables\Columns\DateTimeColumn;
use Boy132\Billing\Enums\CouponType;
use Boy132\Billing\Filament\Admin\Resources\Coupons\Pages\CreateCoupon;
use Boy132\Billing\Filament\Admin\Resources\Coupons\Pages\EditCoupon;
use Boy132\Billing\Filament\Admin\Resources\Coupons\Pages\ListCoupons;
use Boy132\Billing\Models\Coupon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-receipt-tax';

    protected static string|\UnitEnum|null $navigationGroup = 'Billing';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->count() ?: null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('code')
                    ->placeholder('Leave empty to generate random code')
                    ->unique(),
                ToggleButtons::make('coupon_type')
                    ->dehydrated()
                    ->live()
                    ->inline()
                    ->options(CouponType::class)
                    ->default(CouponType::Amount)
                    ->afterStateUpdated(function (Set $set) {
                        $set('amount_off', null);
                        $set('percent_off', null);
                    }),
                TextInput::make('amount_off')
                    ->visible(fn (Get $get) => $get('coupon_type') === CouponType::Amount)
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->suffix(config('billing.currency')),
                TextInput::make('percent_off')
                    ->visible(fn (Get $get) => $get('coupon_type') === CouponType::Percentage)
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(100)
                    ->suffix('%'),
                TextInput::make('max_redemptions')
                    ->placeholder('Leave empty for no redemption limit')
                    ->nullable()
                    ->numeric()
                    ->minValue(1),
                DateTimePicker::make('redeem_by')
                    ->placeholder('Leave empty for no time constraint')
                    ->native(false)
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('code')
                    ->badge()
                    ->sortable(),
                TextColumn::make('coupon')
                    ->badge()
                    ->state(fn (Coupon $coupon) => $coupon->amount_off ? $coupon->amount_off . ' ' . config('billing.currency') : $coupon->percent_off . ' %'),
                TextColumn::make('max_redemptions')
                    ->placeholder('No redemption limit')
                    ->sortable(),
                DateTimeColumn::make('redeem_by')
                    ->placeholder('No time constraint')
                    ->sortable()
                    ->since(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                CreateAction::make()
                    ->createAnother(false),
                BulkActionGroup::make([
                    DeleteBulkAction::make('exclude_bulk_delete'),
                ]),
            ])
            ->emptyStateHeading('No Coupons')
            ->emptyStateDescription('')
            ->emptyStateIcon('tabler-receipt-tax');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCoupons::route('/'),
            'create' => CreateCoupon::route('/create'),
            'edit' => EditCoupon::route('/{record}/edit'),
        ];
    }
}
