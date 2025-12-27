<?php

namespace FlexKleks\ServerFolders\Filament\App\Resources\ServerFolders;

use App\Models\Role;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use FlexKleks\ServerFolders\Filament\App\Resources\ServerFolders\Pages\ManageServerFolders;
use FlexKleks\ServerFolders\Filament\App\Resources\ServerFolders\Pages\ViewServerFolder;
use FlexKleks\ServerFolders\Models\ServerFolder;
use Illuminate\Database\Eloquent\Builder;

class ServerFolderResource extends Resource
{
    protected static ?string $model = ServerFolder::class;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-folder';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return trans('server-folders::messages.folders');
    }

    public static function getModelLabel(): string
    {
        return trans('server-folders::messages.folder');
    }

    public static function getPluralModelLabel(): string
    {
        return trans('server-folders::messages.folders');
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->count() ?: null;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->visibleTo(auth()->user());
    }

    public static function getNavigationItems(): array
    {
        $items = parent::getNavigationItems();

        // Add folder items to navigation
        if (auth()->check()) {
            $folders = ServerFolder::visibleTo(auth()->user())
                ->withCount('servers')
                ->orderBy('sort_order')
                ->get();

            foreach ($folders as $folder) {
                $isOwner = $folder->user_id === auth()->id();

                $items[] = NavigationItem::make($folder->name)
                    ->icon($folder->is_shared && !$isOwner ? 'tabler-folder-share' : 'tabler-folder-filled')
                    ->badge($folder->servers_count ?: null)
                    ->url(static::getUrl('view', ['record' => $folder]))
                    ->isActiveWhen(fn () => request()->route('record') == $folder->id)
                    ->sort(10 + $folder->sort_order);
            }
        }

        return $items;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ColorColumn::make('color')
                    ->label('')
                    ->width('40px'),
                TextColumn::make('name')
                    ->label(trans('server-folders::messages.folder_name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('servers_count')
                    ->label(trans('server-folders::messages.servers'))
                    ->counts('servers')
                    ->badge()
                    ->color('gray'),
                IconColumn::make('is_shared')
                    ->label(trans('server-folders::messages.shared'))
                    ->boolean()
                    ->trueIcon('tabler-share')
                    ->falseIcon('tabler-lock')
                    ->trueColor('success')
                    ->falseColor('gray'),
                TextColumn::make('user.username')
                    ->label(trans('server-folders::messages.owner'))
                    ->visible(fn () => ServerFolder::visibleTo(auth()->user())->where('user_id', '!=', auth()->id())->exists()),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordUrl(fn (ServerFolder $record) => static::getUrl('view', ['record' => $record]))
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()
                        ->visible(fn (ServerFolder $record) => $record->isEditableBy(auth()->user())),
                    DeleteAction::make()
                        ->visible(fn (ServerFolder $record) => $record->isEditableBy(auth()->user())),
                ]),
            ])
            ->emptyStateIcon('tabler-folder')
            ->emptyStateDescription(trans('server-folders::messages.no_folders_desc'))
            ->emptyStateHeading(trans('server-folders::messages.no_folders'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(trans('server-folders::messages.folder_name'))
                    ->required()
                    ->maxLength(50),
                ColorPicker::make('color')
                    ->label(trans('server-folders::messages.color')),
                Toggle::make('is_shared')
                    ->label(trans('server-folders::messages.share_folder'))
                    ->helperText(trans('server-folders::messages.share_folder_hint'))
                    ->live(),
                Select::make('roles')
                    ->label(trans('server-folders::messages.shared_with_roles'))
                    ->helperText(trans('server-folders::messages.shared_with_roles_hint'))
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->options(Role::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->visible(fn ($get) => $get('is_shared')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageServerFolders::route('/'),
            'view' => ViewServerFolder::route('/{record}'),
        ];
    }
}
