<?php

namespace Boy132\ServerTags\Filament\Admin\Resources\Servers\RelationManagers;

use App\Models\Server;
use Boy132\ServerTags\Models\ServerTag;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * @method Server getOwnerRecord()
 */
class ServerTagRelationManager extends RelationManager
{
    protected static string $relationship = 'serverTags';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return trans_choice('server-tags::strings.tag', 2);
    }

    public function table(Table $table): Table
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
                    ->badge()
                    ->color('gray'),
                TextColumn::make('description')
                    ->label(trans('server-tags::strings.description'))
                    ->placeholder(trans('server-tags::strings.no_description'))
                    ->limit(50),
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect(),
            ])
            ->recordActions([
                DetachAction::make(),
            ])
            ->emptyStateIcon('tabler-tags')
            ->emptyStateDescription('')
            ->emptyStateHeading(trans('server-tags::strings.no_tags'));
    }
}