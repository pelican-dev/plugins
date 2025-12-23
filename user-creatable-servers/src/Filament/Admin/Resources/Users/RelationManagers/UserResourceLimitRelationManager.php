<?php

namespace Boy132\UserCreatableServers\Filament\Admin\Resources\Users\RelationManagers;

use Boy132\UserCreatableServers\Filament\Admin\Resources\UserResourceLimits\UserResourceLimitsResource;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class UserResourceLimitRelationManager extends RelationManager
{
    protected static string $relationship = 'userResourceLimits';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        if (static::shouldSkipAuthorization()) {
            return true;
        }

        return UserResourceLimitsResource::canViewAny();
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(trans_choice('user-creatable-servers::strings.user_resource_limits', 2))
            ->columns([
                TextColumn::make('cpu')
                    ->label(trans('user-creatable-servers::strings.cpu'))
                    ->badge()
                    ->suffix('%'),
                TextColumn::make('memory')
                    ->label(trans('user-creatable-servers::strings.memory'))
                    ->badge()
                    ->suffix(config('panel.use_binary_prefix') ? ' MiB' : ' MB'),
                TextColumn::make('disk')
                    ->label(trans('user-creatable-servers::strings.disk'))
                    ->badge()
                    ->suffix(config('panel.use_binary_prefix') ? ' MiB' : ' MB'),
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

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
}
