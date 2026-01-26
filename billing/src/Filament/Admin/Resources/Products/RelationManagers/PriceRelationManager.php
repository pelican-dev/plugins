<?php

namespace Boy132\Billing\Filament\Admin\Resources\Products\RelationManagers;

use Boy132\Billing\Enums\PriceInterval;
use Boy132\Billing\Models\Product;
use Boy132\Billing\Models\ProductPrice;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * @method Product getOwnerRecord()
 */
class PriceRelationManager extends RelationManager
{
    protected static string $relationship = 'prices';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->label('Internal Name')
                    ->columnSpanFull(),
                TextInput::make('cost')
                    ->required()
                    ->suffix(config('billing.currency'))
                    ->numeric()
                    ->minValue(0),
                Toggle::make('renewable')
                    ->label('Can be renewed?')
                    ->inline(false),
                Select::make('interval_type')
                    ->required()
                    ->selectablePlaceholder(false)
                    ->options(PriceInterval::class),
                TextInput::make('interval_value')
                    ->required()
                    ->numeric()
                    ->minValue(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Internal Name')
                    ->sortable(),
                TextColumn::make('cost')
                    ->sortable()
                    ->state(fn (ProductPrice $price) => $price->formatCost()),
                IconColumn::make('renewable')
                    ->label('Can be renewed?')
                    ->boolean(),
                TextColumn::make('interval')
                    ->state(fn (ProductPrice $price) => $price->interval_value . ' ' . $price->interval_type->name),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Create Price')
                    ->createAnother(false),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->emptyStateHeading('No Prices')
            ->emptyStateDescription('');
    }
}
