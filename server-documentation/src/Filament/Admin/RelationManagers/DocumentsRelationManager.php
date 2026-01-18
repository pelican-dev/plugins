<?php

declare(strict_types=1);

namespace Starter\ServerDocumentation\Filament\Admin\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Starter\ServerDocumentation\Filament\Admin\Resources\DocumentResource;
use Starter\ServerDocumentation\Filament\Concerns\HasDocumentTableColumns;
use Starter\ServerDocumentation\Models\Document;

class DocumentsRelationManager extends RelationManager
{
    use HasDocumentTableColumns;

    protected static string $relationship = 'documents';

    protected static string|\BackedEnum|null $icon = 'tabler-file-text';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return trans('server-documentation::strings.document.plural');
    }

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
            ->emptyStateHeading(trans('server-documentation::strings.relation_managers.no_documents_linked'))
            ->emptyStateDescription(trans('server-documentation::strings.relation_managers.attach_documents_description'))
            ->emptyStateIcon('tabler-file-off');
    }
}
