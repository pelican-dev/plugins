<?php

namespace Boy132\PlayerCounter\Filament\Admin\Resources;

use Boy132\PlayerCounter\Filament\Admin\Resources\GameQueryResource\Pages\ManageGameQueries;
use Boy132\PlayerCounter\Models\GameQuery;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GameQueryResource extends Resource
{
    protected static ?string $model = GameQuery::class;

    protected static ?string $navigationIcon = 'tabler-device-desktop-search';

    public static function getNavigationLabel(): string
    {
        return trans_choice('playercounter::query.query', 2);
    }

    public static function getModelLabel(): string
    {
        return trans_choice('playercounter::query.query', 1);
    }

    public static function getPluralModelLabel(): string
    {
        return trans_choice('playercounter::query.query', 2);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count() ?: null;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('query_type')
                    ->label(trans('playercounter::query.type'))
                    ->badge(),
                TextColumn::make('query_port_offset')
                    ->label(trans('playercounter::query.port_offset'))
                    ->placeholder(trans('playercounter::query.no_offset')),
                TextColumn::make('eggs.name')
                    ->label(trans('playercounter::query.eggs'))
                    ->placeholder(trans('playercounter::query.no_eggs'))
                    ->icon('tabler-eggs')
                    ->badge(),
            ])
            ->actions([
                ViewAction::make()
                    ->hidden(fn ($record) => static::canEdit($record)),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->emptyStateIcon('tabler-device-desktop-search')
            ->emptyStateDescription('')
            ->emptyStateHeading(trans('playercounter::query.no_queries'));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('query_type')
                    ->label(trans('playercounter::query.type'))
                    ->required(),
                TextInput::make('query_port_offset')
                    ->label(trans('playercounter::query.port_offset'))
                    ->placeholder(trans('playercounter::query.no_offset'))
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('query_type')
                    ->label(trans('playercounter::query.type')),
                TextEntry::make('query_port_offset')
                    ->label(trans('playercounter::query.port_offset'))
                    ->placeholder(trans('playercounter::query.no_offset'))
                    ->numeric(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageGameQueries::route('/'),
        ];
    }
}
