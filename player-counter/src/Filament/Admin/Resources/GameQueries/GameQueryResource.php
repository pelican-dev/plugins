<?php

namespace Boy132\PlayerCounter\Filament\Admin\Resources\GameQueries;

use Boy132\PlayerCounter\Enums\GameQueryType;
use Boy132\PlayerCounter\Filament\Admin\Resources\GameQueries\Pages\ManageGameQueries;
use Boy132\PlayerCounter\Models\GameQuery;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GameQueryResource extends Resource
{
    protected static ?string $model = GameQuery::class;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-device-desktop-search';

    public static function getNavigationLabel(): string
    {
        return trans_choice('player-counter::query.query', 2);
    }

    public static function getModelLabel(): string
    {
        return trans_choice('player-counter::query.query', 1);
    }

    public static function getPluralModelLabel(): string
    {
        return trans_choice('player-counter::query.query', 2);
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->count() ?: null;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('query_type')
                    ->label(trans('player-counter::query.type'))
                    ->badge(),
                TextColumn::make('query_port_offset')
                    ->label(trans('player-counter::query.port_offset'))
                    ->placeholder(trans('player-counter::query.no_offset')),
                TextColumn::make('eggs.name')
                    ->label(trans('player-counter::query.eggs'))
                    ->placeholder(trans('player-counter::query.no_eggs'))
                    ->icon('tabler-eggs')
                    ->badge(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->hidden(fn ($record) => static::canEdit($record)),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->emptyStateIcon('tabler-device-desktop-search')
            ->emptyStateDescription('')
            ->emptyStateHeading(trans('player-counter::query.no_queries'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('query_type')
                    ->label(trans('player-counter::query.type'))
                    ->required()
                    ->options(GameQueryType::class)
                    ->selectablePlaceholder(false)
                    ->preload()
                    ->searchable(),
                TextInput::make('query_port_offset')
                    ->label(trans('player-counter::query.port_offset'))
                    ->placeholder(trans('player-counter::query.no_offset'))
                    ->numeric()
                    ->nullable()
                    ->minValue(1)
                    ->maxValue(65535 - 1024),
                Select::make('eggs')
                    ->label(trans('admin/mount.eggs'))
                    ->relationship('eggs', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->columnSpanFull(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('query_type')
                    ->label(trans('player-counter::query.type')),
                TextEntry::make('query_port_offset')
                    ->label(trans('player-counter::query.port_offset'))
                    ->placeholder(trans('player-counter::query.no_offset'))
                    ->numeric(),
                TextEntry::make('eggs.name')
                    ->label(trans('admin/mount.eggs'))
                    ->placeholder(trans('player-counter::query.no_eggs'))
                    ->badge()
                    ->columnSpanFull(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageGameQueries::route('/'),
        ];
    }
}
