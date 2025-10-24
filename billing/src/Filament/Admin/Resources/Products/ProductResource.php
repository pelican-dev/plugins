<?php

namespace Boy132\Billing\Filament\Admin\Resources\Products;

use Boy132\Billing\Filament\Admin\Resources\Products\Pages\CreateProduct;
use Boy132\Billing\Filament\Admin\Resources\Products\Pages\EditProduct;
use Boy132\Billing\Filament\Admin\Resources\Products\Pages\ListProducts;
use Boy132\Billing\Models\Product;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Number;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-package';

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
                Textarea::make('description')
                    ->autosize(),
                Fieldset::make('Server')
                    ->schema([
                        Select::make('egg_id')
                            ->prefixIcon('tabler-egg')
                            ->label('Egg')
                            ->required()
                            ->relationship('egg', 'name')
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),
                        TextInput::make('cpu')
                            ->prefixIcon('tabler-cpu')
                            ->label('CPU')
                            ->required()
                            ->suffix('%')
                            ->numeric()
                            ->minValue(0)
                            ->hint('Set to 0 for unlimited.'),
                        TextInput::make('memory')
                            ->prefixIcon('tabler-database')
                            ->required()
                            ->suffix(config('panel.use_binary_prefix') ? 'MiB' : 'MB')
                            ->numeric()
                            ->minValue(0)
                            ->hint('Set to 0 for unlimited.'),
                        TextInput::make('disk')
                            ->prefixIcon('tabler-folder')
                            ->required()
                            ->suffix(config('panel.use_binary_prefix') ? 'MiB' : 'MB')
                            ->numeric()
                            ->minValue(0)
                            ->hint('Set to 0 for unlimited.'),
                        TextInput::make('swap')
                            ->prefixIcon('tabler-file-database')
                            ->required()
                            ->suffix(config('panel.use_binary_prefix') ? 'MiB' : 'MB')
                            ->numeric()
                            ->minValue(0)
                            ->hint('Set to -1 for unlimited or 0 for no swap.'),
                    ]),
                Fieldset::make('Deployment')
                    ->columns(2)
                    ->schema([
                        TagsInput::make('ports'),
                        TagsInput::make('tags'),
                    ]),
                Fieldset::make('Limits')
                    ->columns(3)
                    ->schema([
                        TextInput::make('allocation_limit')
                            ->prefixIcon('tabler-network')
                            ->required()
                            ->numeric()
                            ->minValue(0),
                        TextInput::make('database_limit')
                            ->prefixIcon('tabler-database')
                            ->required()
                            ->numeric()
                            ->minValue(0),
                        TextInput::make('backup_limit')
                            ->prefixIcon('tabler-copy-check')
                            ->required()
                            ->numeric()
                            ->minValue(0),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                TextColumn::make('name')
                    ->description(fn (Product $product) => $product->description)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('egg.name')
                    ->sortable()
                    ->icon('tabler-egg')
                    ->url(fn (Product $product): string => route('filament.admin.resources.eggs.edit', ['record' => $product->egg])),
                TextColumn::make('cpu')
                    ->label('CPU')
                    ->numeric()
                    ->formatStateUsing(fn ($state) => $state === 0 ? 'Unlimited' : $state . ' %'),
                TextColumn::make('memory')
                    ->numeric()
                    ->formatStateUsing(fn ($state) => $state === 0 ? 'Unlimited' : Number::format($state / (config('panel.use_binary_prefix') ? 1024 : 1000), 2, locale: auth()->user()->language) . (config('panel.use_binary_prefix') ? ' GiB' : ' GB')),
                TextColumn::make('disk')
                    ->numeric()
                    ->formatStateUsing(fn ($state) => $state === 0 ? 'Unlimited' : Number::format($state / (config('panel.use_binary_prefix') ? 1024 : 1000), 2, locale: auth()->user()->language) . (config('panel.use_binary_prefix') ? ' GiB' : ' GB')),
                TextColumn::make('swap')
                    ->numeric()
                    ->formatStateUsing(fn ($state) => $state === 0 ? 'No Swap' : Number::format($state / (config('panel.use_binary_prefix') ? 1024 : 1000), 2, locale: auth()->user()->language) . (config('panel.use_binary_prefix') ? ' GiB' : ' GB')),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->emptyStateHeading('No Products')
            ->emptyStateDescription('');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}
