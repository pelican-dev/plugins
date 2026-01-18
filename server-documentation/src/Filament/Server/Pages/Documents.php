<?php

declare(strict_types=1);

namespace Starter\ServerDocumentation\Filament\Server\Pages;

use App\Models\Server;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Starter\ServerDocumentation\Models\Document;
use Starter\ServerDocumentation\Services\DocumentService;

class Documents extends Page
{
    protected static ?int $navigationSort = 50;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-file-text';

    protected string $view = 'server-documentation::filament.server.pages.documents';

    public ?Document $selectedDocument = null;

    public static function getNavigationLabel(): string
    {
        return trans('server-documentation::strings.navigation.documents');
    }

    public function getTitle(): string
    {
        return trans('server-documentation::strings.server_panel.title');
    }

    public static function canAccess(): bool
    {
        /** @var Server|null $server */
        $server = Filament::getTenant();

        if ($server === null) {
            return false;
        }

        return static::getDocumentsForServer($server)->isNotEmpty();
    }

    public function mount(): void
    {
        $documents = $this->getDocuments();

        if ($documents->isNotEmpty() && !$this->selectedDocument) {
            $this->selectedDocument = $documents->first();
        }
    }

    public function selectDocument(int $documentId): void
    {
        $document = $this->getDocuments()->firstWhere('id', $documentId);

        if ($document) {
            /** @var Server $server */
            $server = Filament::getTenant();
            $user = user();

            if ($user && $user->cannot('viewOnServer', [$document, $server])) {
                $document = null;
            }
        }

        $this->selectedDocument = $document;
    }

    public function getDocuments(): Collection
    {
        /** @var Server $server */
        $server = Filament::getTenant();

        return static::getDocumentsForServer($server);
    }

    protected static function getDocumentsForServer(Server $server): Collection
    {
        return app(DocumentService::class)->getDocumentsForServer($server, user());
    }
}
