<?php

namespace Boy132\UserCreatableServers\Filament\Admin\Resources\UserResourceLimits;

use App\Filament\Admin\Resources\Users\Pages\EditUser;
use App\Models\User;
use Boy132\UserCreatableServers\Filament\Admin\Resources\UserResourceLimits\Pages\ManageUserResourceLimits;
use Boy132\UserCreatableServers\Models\UserResourceLimits;
use Filament\Actions\ActionGroup;
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

class UserResourceLimitsResource extends Resource
{
    protected static ?string $model = UserResourceLimits::class;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-cube-plus';

    public static function getNavigationLabel(): string
    {
        return trans_choice('user-creatable-servers::strings.user_resource_limits', 2);
    }

    public static function getModelLabel(): string
    {
        return trans_choice('user-creatable-servers::strings.user_resource_limits', 1);
    }

    public static function getPluralModelLabel(): string
    {
        return trans_choice('user-creatable-servers::strings.user_resource_limits', 2);
    }

    public static function getNavigationGroup(): ?string
    {
        return config('panel.filament.top-navigation', false) ? null : trans('admin/dashboard.user');
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->count() ?: null;
    }

    public static function table(Table $table): Table
    {
        $suffix = config('panel.use_binary_prefix') ? ' MiB' : ' MB';

        return $table
            ->columns([
                TextColumn::make('user.username')
                    ->label(trans_choice('user-creatable-servers::strings.user', 1))
                    ->icon('tabler-user')
                    ->url(fn (UserResourceLimits $userResourceLimits) => auth()->user()->can('update', $userResourceLimits->user) ? EditUser::getUrl(['record' => $userResourceLimits->user]) : null),
                TextColumn::make('cpu')
                    ->label(trans('user-creatable-servers::strings.cpu'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state > 0 ? $state . '%' : trans('user-creatable-servers::strings.unlimited')),
                TextColumn::make('memory')
                    ->label(trans('user-creatable-servers::strings.memory'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state > 0 ? $state . $suffix : trans('user-creatable-servers::strings.unlimited')),
                TextColumn::make('disk')
                    ->label(trans('user-creatable-servers::strings.disk'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state > 0 ? $state . $suffix : trans('user-creatable-servers::strings.unlimited')),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->hidden(fn ($record) => static::canEdit($record)),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->emptyStateIcon('tabler-cube-plus')
            ->emptyStateDescription('');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label(trans_choice('user-creatable-servers::strings.user', 1))
                    ->required()
                    ->prefixIcon('tabler-user')
                    ->unique()
                    ->relationship('user', 'username')
                    ->searchable(['username', 'email'])
                    ->getOptionLabelFromRecordUsing(fn (User $user) => "$user->username ($user->email)")
                    ->selectablePlaceholder(false)
                    ->hiddenOn('edit'),
                TextInput::make('cpu')
                    ->label(trans('user-creatable-servers::strings.cpu'))
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->suffix('%')
                    ->hint(trans('user-creatable-servers::strings.hint_unlimited')),
                TextInput::make('memory')
                    ->label(trans('user-creatable-servers::strings.memory'))
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->suffix(config('panel.use_binary_prefix') ? 'MiB' : 'MB')
                    ->hint(trans('user-creatable-servers::strings.hint_unlimited')),
                TextInput::make('disk')
                    ->label(trans('user-creatable-servers::strings.disk'))
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->suffix(config('panel.use_binary_prefix') ? 'MiB' : 'MB')
                    ->hint(trans('user-creatable-servers::strings.hint_unlimited')),
                TextInput::make('server_limit')
                    ->label(trans('user-creatable-servers::strings.server_limit'))
                    ->numeric()
                    ->nullable()
                    ->placeholder(trans('user-creatable-servers::strings.no_limit')),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.username')
                    ->label(trans_choice('user-creatable-servers::strings.user', 1))
                    ->columnSpanFull(),
                TextEntry::make('cpu')
                    ->label(trans('user-creatable-servers::strings.cpu'))
                    ->badge(),
                TextEntry::make('memory')
                    ->label(trans('user-creatable-servers::strings.memory'))
                    ->badge(),
                TextEntry::make('disk')
                    ->label(trans('user-creatable-servers::strings.disk'))
                    ->badge(),
                TextEntry::make('server_limit')
                    ->label(trans('user-creatable-servers::strings.server_limit'))
                    ->badge()
                    ->placeholder(trans('user-creatable-servers::strings.no_limit')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageUserResourceLimits::route('/'),
        ];
    }
}
