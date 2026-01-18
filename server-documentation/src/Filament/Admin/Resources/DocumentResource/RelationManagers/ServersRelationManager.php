<?php

declare(strict_types=1);

namespace Starter\ServerDocumentation\Filament\Admin\Resources\DocumentResource\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ServersRelationManager extends RelationManager
{
    protected static string $relationship = 'servers';

    protected static string|\BackedEnum|null $icon = 'tabler-server';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return trans('server-documentation::strings.relation_managers.linked_servers');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->reorderable('pivot.sort_order')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('node.name')
                    ->label(trans('server-documentation::strings.server.node'))
                    ->sortable(),

                TextColumn::make('user.username')
                    ->label(trans('server-documentation::strings.server.owner')),

                TextColumn::make('pivot.sort_order')
                    ->label(trans('server-documentation::strings.document.sort_order'))
                    ->sortable(),
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['name', 'uuid', 'uuid_short'])
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->helperText(trans('server-documentation::strings.relation_managers.sort_order_helper')),
                    ]),
            ])
            ->recordActions([
                DetachAction::make(),
            ])
            ->groupedBulkActions([
                DetachBulkAction::make(),
            ])
            ->emptyStateHeading(trans('server-documentation::strings.relation_managers.no_servers_linked'))
            ->emptyStateDescription(trans('server-documentation::strings.relation_managers.attach_servers_description'))
            ->emptyStateIcon('tabler-server-off');
    }
}
