<?php

declare(strict_types=1);

namespace Starter\ServerDocumentation\Filament\Admin\Resources\DocumentResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Starter\ServerDocumentation\Enums\DocumentType;
use Starter\ServerDocumentation\Filament\Admin\Resources\DocumentResource;
use Starter\ServerDocumentation\Models\Document;
use Starter\ServerDocumentation\Services\MarkdownConverter;

class ListDocuments extends ListRecords
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        $maxFileSize = config('server-documentation.max_import_size', 512);

        return [
            Action::make('import')
                ->label(trans('server-documentation::strings.actions.import'))
                ->icon('tabler-upload')
                ->color('gray')
                ->form([
                    FileUpload::make('markdown_file')
                        ->label(trans('server-documentation::strings.import.file_label'))
                        ->helperText(trans('server-documentation::strings.import.file_helper') . " (max {$maxFileSize}KB)")
                        ->acceptedFileTypes(['text/markdown', 'text/plain', '.md'])
                        ->maxSize($maxFileSize)
                        ->required()
                        ->storeFiles(false),
                    Toggle::make('use_frontmatter')
                        ->label(trans('server-documentation::strings.import.use_frontmatter'))
                        ->helperText(trans('server-documentation::strings.import.use_frontmatter_helper'))
                        ->default(true),
                ])
                ->action(function (array $data): void {
                    $this->importMarkdownFile($data);
                }),
            Action::make('help')
                ->label(trans('server-documentation::strings.permission_guide.title'))
                ->icon('tabler-help')
                ->color('gray')
                ->modalHeading(trans('server-documentation::strings.permission_guide.modal_heading'))
                ->modalDescription(new HtmlString(
                    view('server-documentation::filament.partials.permission-guide', ['showExamples' => true])->render() // @phpstan-ignore argument.type
                ))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel(trans('server-documentation::strings.actions.close')),
            CreateAction::make(),
        ];
    }

    /**
     * Import a Markdown file and create a new document.
     *
     * @phpstan-param array<string, mixed> $data
     */
    protected function importMarkdownFile(array $data): void
    {
        $converter = app(MarkdownConverter::class);

        /** @var TemporaryUploadedFile $file */
        $file = $data['markdown_file'];

        $maxSize = config('server-documentation.max_import_size', 512) * 1024;
        if ($file->getSize() > $maxSize) {
            Notification::make()
                ->title(trans('server-documentation::strings.import.error'))
                ->body(trans('server-documentation::strings.import.file_too_large'))
                ->danger()
                ->send();

            return;
        }

        $content = file_get_contents($file->getRealPath());
        if ($content === false) {
            Notification::make()
                ->title(trans('server-documentation::strings.import.error'))
                ->body(trans('server-documentation::strings.import.file_read_error'))
                ->danger()
                ->send();

            return;
        }

        $useFrontmatter = $data['use_frontmatter'] ?? true;
        $metadata = [];
        $markdownContent = $content;

        if ($useFrontmatter) {
            [$metadata, $markdownContent] = $converter->parseFrontmatter($content);
        }

        $htmlContent = $converter->toHtml($markdownContent);

        $title = $metadata['title']
            ?? $this->extractTitleFromMarkdown($markdownContent)
            ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        $slug = $metadata['slug'] ?? Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;
        while (Document::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        $type = $this->normalizeDocumentType($metadata['type'] ?? null);

        $document = Document::create([
            'title' => $title,
            'slug' => $slug,
            'content' => $htmlContent,
            'type' => $type,
            'is_global' => filter_var($metadata['is_global'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'is_published' => filter_var($metadata['is_published'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'sort_order' => (int) ($metadata['sort_order'] ?? 0),
            'author_id' => auth()->id(),
            'last_edited_by' => auth()->id(),
        ]);

        Notification::make()
            ->title(trans('server-documentation::strings.import.success'))
            ->body(trans('server-documentation::strings.import.success_body', ['title' => $document->title]))
            ->success()
            ->send();

        $this->redirect(DocumentResource::getUrl('edit', ['record' => $document]));
    }

    /**
     * Extract title from first H1 heading in markdown.
     */
    protected function extractTitleFromMarkdown(string $markdown): ?string
    {
        if (preg_match('/^#\s+(.+)$/m', $markdown, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    /**
     * Normalize document type from import, validating against allowed types.
     */
    protected function normalizeDocumentType(?string $type): string
    {
        if ($type === null) {
            return DocumentType::Player->value;
        }

        if (DocumentType::isValid($type)) {
            $enumType = DocumentType::tryFromLegacy($type);

            return $enumType !== null ? $enumType->value : DocumentType::Player->value;
        }

        logger()->warning('Invalid document type in import', [
            'type' => $type,
            'defaulted_to' => DocumentType::Player->value,
        ]);

        return DocumentType::Player->value;
    }
}
