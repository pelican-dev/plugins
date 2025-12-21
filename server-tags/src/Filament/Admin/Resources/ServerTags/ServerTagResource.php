<?php

namespace Boy132\ServerTags\Filament\Admin\Resources\ServerTags;

use Boy132\ServerTags\Filament\Admin\Resources\ServerTags\Pages\ManageServerTags;
use Boy132\ServerTags\Models\ServerTag;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ServerTagResource extends Resource
{
    protected static ?string $model = ServerTag::class;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-tags';

    public static function getNavigationLabel(): string
    {
        return trans_choice('server-tags::strings.tag', 2);
    }

    public static function getModelLabel(): string
    {
        return trans_choice('server-tags::strings.tag', 1);
    }

    public static function getPluralModelLabel(): string
    {
        return trans_choice('server-tags::strings.tag', 2);
    }

    public static function getNavigationGroup(): ?string
    {
        return trans('admin/dashboard.advanced');
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->count() ?: null;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(trans('server-tags::strings.name'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (ServerTag $tag) => Color::hex($tag->color)),
                TextColumn::make('slug')
                    ->label(trans('server-tags::strings.slug'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('description')
                    ->label(trans('server-tags::strings.description'))
                    ->placeholder(trans('server-tags::strings.no_description'))
                    ->limit(50),
                TextColumn::make('servers_count')
                    ->label(trans('server-tags::strings.servers_count'))
                    ->counts('servers')
                    ->badge()
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->hidden(fn ($record) => static::canEdit($record)),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->emptyStateIcon('tabler-tags')
            ->emptyStateDescription('')
            ->emptyStateHeading(trans('server-tags::strings.no_tags'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(trans('server-tags::strings.name'))
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, $set, $get) => !$get('slug') ? $set('slug', Str::slug($state)) : null)
                    ->maxLength(255),
                TextInput::make('slug')
                    ->label(trans('server-tags::strings.slug'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->rules(['alpha_dash']),
                ColorPicker::make('color')
                    ->label(trans('server-tags::strings.color'))
                    ->required()
                    ->hex()
                    ->default('#3b82f6'),
                Textarea::make('description')
                    ->label(trans('server-tags::strings.description'))
                    ->nullable()
                    ->rows(3)
                    ->autosize()
                    ->columnSpanFull(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label(trans('server-tags::strings.name'))
                    ->badge()
                    ->color(fn (ServerTag $tag) => Color::hex($tag->color)),
                TextEntry::make('slug')
                    ->label(trans('server-tags::strings.slug'))
                    ->badge()
                    ->color('gray'),
                TextEntry::make('color')
                    ->label(trans('server-tags::strings.color'))
                    ->badge()
                    ->color(fn (ServerTag $tag) => Color::hex($tag->color)),
                TextEntry::make('description')
                    ->label(trans('server-tags::strings.description'))
                    ->placeholder(trans('server-tags::strings.no_description'))
                    ->columnSpanFull(),
                TextEntry::make('servers_count')
                    ->label(trans('server-tags::strings.servers_count'))
                    ->state(fn (ServerTag $tag) => $tag->servers()->count())
                    ->badge(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageServerTags::route('/'),
        ];
    }
}