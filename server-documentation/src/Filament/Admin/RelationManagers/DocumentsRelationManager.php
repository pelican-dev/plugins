<?php

declare(strict_types=1);

namespace Starter\ServerDocumentation\Filament\Admin\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\DetachBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Starter\ServerDocumentation\Filament\Admin\Resources\DocumentResource;
use Starter\ServerDocumentation\Filament\Concerns\HasDocumentTableColumns;
use Starter\ServerDocumentation\Models\Document;

class DocumentsRelationManager extends RelationManager
{
    use HasDocumentTableColumns;

    protected static string $relationship = 'documents';

    protected static ?string $title = 'Documents';

    protected static string|\BackedEnum|null $icon = 'tabler-file-text';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->reorderable('pivot.sort_order')
            ->columns([
                static::getDocumentTitleColumn(40),
                static::getDocumentTypeColumn(),
                static::getDocumentGlobalColumn(),
                static::getDocumentPublishedColumn(),

                TextColumn::make('pivot.sort_order')
                    ->label(trans('server-documentation::strings.document.sort_order'))
                    ->sortable(),

                static::getDocumentUpdatedAtColumn(),
            ])
            ->filters([
                static::getDocumentTypeFilter(),
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        TextInput::make('sort_order')
                            ->label(trans('server-documentation::strings.document.sort_order'))
                            ->numeric()
                            ->default(0)
                            ->helperText(trans('server-documentation::strings.relation_managers.sort_order_helper')),
                    ]),
                CreateAction::make()
                    ->mutateFormDataUsing(fn (array $data): array => [
                        ...$data,
                        'author_id' => auth()->id(),
                    ]),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (Document $record) => DocumentResource::getUrl('edit', ['record' => $record])),
                DetachAction::make(),
            ])
            ->groupedBulkActions([
                DetachBulkAction::make(),
            ])
            ->emptyStateHeading(trans('server-documentation::strings.relation_managers.no_servers_linked'))
            ->emptyStateDescription(trans('server-documentation::strings.relation_managers.attach_servers_description'))
            ->emptyStateIcon('tabler-file-off');
    }
}
