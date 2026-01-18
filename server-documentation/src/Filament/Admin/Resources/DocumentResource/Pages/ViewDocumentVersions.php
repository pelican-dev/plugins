<?php

declare(strict_types=1);

namespace Starter\ServerDocumentation\Filament\Admin\Resources\DocumentResource\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Starter\ServerDocumentation\Filament\Admin\Resources\DocumentResource;
use Starter\ServerDocumentation\Models\Document;
use Starter\ServerDocumentation\Models\DocumentVersion;

class ViewDocumentVersions extends Page implements HasTable
{
    use InteractsWithRecord;
    use InteractsWithTable;

    protected static string $resource = DocumentResource::class;

    protected string $view = 'server-documentation::filament.pages.document-versions';

    public function mount(int|string $record): void
    {
        /** @var Document $resolved */
        $resolved = $this->resolveRecord($record);
        $this->record = $resolved;
    }

    public function getRecord(): Document
    {
        /** @var Document */
        return $this->record;
    }

    public function getTitle(): string|Htmlable
    {
        return trans('server-documentation::strings.versions.title') . ': ' . $this->getRecord()->title;
    }

    public static function getNavigationLabel(): string
    {
        return trans('server-documentation::strings.versions.title');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label(trans('server-documentation::strings.actions.back_to_document'))
                ->icon('tabler-arrow-left')
                ->url(fn () => DocumentResource::getUrl('edit', ['record' => $this->getRecord()])),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => DocumentVersion::query()->where('document_id', $this->getRecord()->id))
            ->columns([
                TextColumn::make('version_number')
                    ->label(trans('server-documentation::strings.versions.version_number'))
                    ->formatStateUsing(fn (int $state): string => 'v' . $state)
                    ->sortable(),

                TextColumn::make('title')
                    ->label(trans('server-documentation::strings.document.title'))
                    ->limit(40),

                TextColumn::make('editor.username')
                    ->label(trans('server-documentation::strings.versions.edited_by'))
                    ->placeholder('Unknown'),

                TextColumn::make('change_summary')
                    ->label(trans('server-documentation::strings.versions.change_summary'))
                    ->limit(50)
                    ->placeholder('-'),

                TextColumn::make('created_at')
                    ->label(trans('server-documentation::strings.versions.date'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('version_number', 'desc')
            ->actions([
                Action::make('preview')
                    ->label(trans('server-documentation::strings.versions.preview'))
                    ->icon('tabler-eye')
                    ->modalHeading(fn (DocumentVersion $record): string => 'v' . $record->version_number . ': ' . $record->title)
                    ->modalContent(fn (DocumentVersion $record): HtmlString => new HtmlString(
                        view('server-documentation::filament.pages.version-preview', ['version' => $record])->render() // @phpstan-ignore argument.type
                    ))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),

                Action::make('restore')
                    ->label(trans('server-documentation::strings.versions.restore'))
                    ->icon('tabler-restore')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading(trans('server-documentation::strings.versions.restore'))
                    ->modalDescription(trans('server-documentation::strings.versions.restore_confirm'))
                    ->action(function (DocumentVersion $record): void {
                        $this->getRecord()->restoreVersion($record);

                        Notification::make()
                            ->title(trans('server-documentation::strings.versions.restored'))
                            ->success()
                            ->send();
                    }),
            ])
            ->emptyStateHeading(trans('server-documentation::strings.messages.no_versions'))
            ->emptyStateIcon('tabler-history-off');
    }
}
