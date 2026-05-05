<?php

namespace Boy132\Billing\Filament\Admin\Resources\Customers;

use App\Models\User;
use Boy132\Billing\Filament\Admin\Resources\Customers\Pages\CreateCustomer;
use Boy132\Billing\Filament\Admin\Resources\Customers\Pages\EditCustomer;
use Boy132\Billing\Filament\Admin\Resources\Customers\Pages\ListCustomers;
use Boy132\Billing\Models\Customer;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use NumberFormatter;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-user-dollar';

    protected static string|\UnitEnum|null $navigationGroup = 'Billing';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->count() ?: null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->prefixIcon('tabler-user')
                    ->label('User')
                    ->required()
                    ->selectablePlaceholder(false)
                    ->relationship('user', 'username')
                    ->searchable(['username', 'email'])
                    ->getOptionLabelFromRecordUsing(fn (User $user) => $user->email . ' | ' . $user->username)
                    ->preload(),
                TextInput::make('balance')
                    ->required()
                    ->suffix(config('billing.currency'))
                    ->numeric()
                    ->minValue(0)
                    ->default(0),
                TextInput::make('first_name')
                    ->required(),
                TextInput::make('last_name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                TextColumn::make('first_name')
                    ->sortable(),
                TextColumn::make('last_name')
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('E-Mail')
                    ->sortable(),
                TextColumn::make('balance')
                    ->numeric()
                    ->formatStateUsing(function ($state) {
                        $formatter = new NumberFormatter(auth()->user()->language, NumberFormatter::CURRENCY);

                        return $formatter->formatCurrency($state, config('billing.currency'));
                    }),
                TextColumn::make('orders_count')
                    ->label('Orders')
                    ->counts('orders')
                    ->badge(),
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
            ->emptyStateHeading('No Customers')
            ->emptyStateDescription('')
            ->emptyStateIcon('tabler-user-dollar');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCustomers::route('/'),
            'create' => CreateCustomer::route('/create'),
            'edit' => EditCustomer::route('/{record}/edit'),
        ];
    }
}
