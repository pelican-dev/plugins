<?php

declare(strict_types=1);

namespace Starter\ServerDocumentation\Filament\Concerns;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Support\Str;
use Starter\ServerDocumentation\Enums\DocumentType;
use Starter\ServerDocumentation\Models\Document;

/**
 * Shared table column definitions for Document resources and relation managers.
 */
trait HasDocumentTableColumns
{
    protected static function getDocumentTitleColumn(int $descriptionLimit = 50): TextColumn
    {
        return TextColumn::make('title')
            ->label(trans('server-documentation::strings.document.title'))
            ->searchable()
            ->sortable()
            ->description(fn (Document $record) => Str::limit(strip_tags($record->content), $descriptionLimit));
    }

    protected static function getDocumentTypeColumn(): TextColumn
    {
        return TextColumn::make('type')
            ->label(trans('server-documentation::strings.document.type'))
            ->badge()
            ->formatStateUsing(fn (string $state): string => DocumentType::formatLabel($state))
            ->color(fn (string $state): string => DocumentType::formatColor($state));
    }

    protected static function getDocumentGlobalColumn(): IconColumn
    {
        return IconColumn::make('is_global')
            ->boolean()
            ->label(trans('server-documentation::strings.document.is_global'))
            ->trueIcon('tabler-world')
            ->falseIcon('tabler-world-off');
    }

    protected static function getDocumentPublishedColumn(): IconColumn
    {
        return IconColumn::make('is_published')
            ->boolean()
            ->label(trans('server-documentation::strings.document.is_published'));
    }

    protected static function getDocumentUpdatedAtColumn(): TextColumn
    {
        return TextColumn::make('updated_at')
            ->label(trans('server-documentation::strings.table.updated_at'))
            ->dateTime()
            ->sortable()
            ->toggleable();
    }

    protected static function getDocumentTypeFilter(): SelectFilter
    {
        return SelectFilter::make('type')
            ->label(trans('server-documentation::strings.document.type'))
            ->options(DocumentType::simpleOptions());
    }

    protected static function getDocumentGlobalFilter(): TernaryFilter
    {
        return TernaryFilter::make('is_global')
            ->label(trans('server-documentation::strings.document.is_global'));
    }

    protected static function getDocumentPublishedFilter(): TernaryFilter
    {
        return TernaryFilter::make('is_published')
            ->label(trans('server-documentation::strings.document.is_published'));
    }
}
