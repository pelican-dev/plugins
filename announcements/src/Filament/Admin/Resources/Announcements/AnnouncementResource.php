<?php

namespace Boy132\Announcements\Filament\Admin\Resources\Announcements;

use Boy132\Announcements\Filament\Admin\Resources\Announcements\Pages\ManageAnnouncements;
use Boy132\Announcements\Models\Announcement;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-speakerphone';

    public static function getNavigationLabel(): string
    {
        return trans_choice('announcements::strings.announcement', 2);
    }

    public static function getModelLabel(): string
    {
        return trans_choice('announcements::strings.announcement', 1);
    }

    public static function getPluralModelLabel(): string
    {
        return trans_choice('announcements::strings.announcement', 2);
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->count() ?: null;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(trans('announcements::strings.title')),
                TextColumn::make('body')
                    ->label(trans('announcements::strings.body'))
                    ->placeholder(trans('announcements::strings.no_body')),
                TextColumn::make('type')
                    ->label(trans('announcements::strings.type'))
                    ->color(fn ($state) => $state)
                    ->badge(),
                TextColumn::make('panels')
                    ->label(trans('announcements::strings.panels'))
                    ->placeholder(trans('announcements::strings.all_panels'))
                    ->badge(),
                TextColumn::make('valid_from')
                    ->label(trans('announcements::strings.valid_from'))
                    ->placeholder(trans('announcements::strings.no_valid_from'))
                    ->dateTime(),
                TextColumn::make('valid_to')
                    ->label(trans('announcements::strings.valid_to'))
                    ->placeholder(trans('announcements::strings.no_valid_to'))
                    ->dateTime(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->hidden(fn ($record) => static::getEditAuthorizationResponse($record)->allowed()),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                CreateAction::make()
                    ->createAnother(false),
            ])
            ->emptyStateIcon('tabler-speakerphone')
            ->emptyStateDescription('')
            ->emptyStateHeading(trans('announcements::strings.no_announcements'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label(trans('announcements::strings.title'))
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('body')
                    ->label(trans('announcements::strings.body'))
                    ->placeholder(trans('announcements::strings.no_body'))
                    ->nullable()
                    ->columnSpanFull(),
                Select::make('type')
                    ->label(trans('announcements::strings.type'))
                    ->selectablePlaceholder(false)
                    ->default('info')
                    ->options([
                        'info' => 'Info',
                        'success' => 'Success',
                        'warning' => 'Warning',
                        'danger' => 'Danger',
                    ]),
                Select::make('panels')
                    ->label(trans('announcements::strings.panels'))
                    ->multiple()
                    ->options([
                        'admin' => 'Admin Area',
                        'server' => 'Client Area',
                        'app' => 'Server List',
                    ]),
                DateTimePicker::make('valid_from')
                    ->label(trans('announcements::strings.valid_from'))
                    ->placeholder(trans('announcements::strings.no_valid_from'))
                    ->nullable(),
                DateTimePicker::make('valid_to')
                    ->label(trans('announcements::strings.valid_to'))
                    ->placeholder(trans('announcements::strings.no_valid_to'))
                    ->nullable(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title')
                    ->label(trans('announcements::strings.title'))
                    ->columnSpanFull(),
                TextEntry::make('body')
                    ->label(trans('announcements::strings.body'))
                    ->placeholder(trans('announcements::strings.no_body'))
                    ->columnSpanFull(),
                TextEntry::make('type')
                    ->label(trans('announcements::strings.type'))
                    ->color(fn ($state) => $state)
                    ->badge(),
                TextEntry::make('panels')
                    ->label(trans('announcements::strings.panels'))
                    ->placeholder(trans('announcements::strings.all_panels'))
                    ->badge(),
                TextEntry::make('valid_from')
                    ->label(trans('announcements::strings.valid_from'))
                    ->placeholder(trans('announcements::strings.no_valid_from')),
                TextEntry::make('valid_to')
                    ->label(trans('announcements::strings.valid_to'))
                    ->placeholder(trans('announcements::strings.no_valid_to')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAnnouncements::route('/'),
        ];
    }
}
