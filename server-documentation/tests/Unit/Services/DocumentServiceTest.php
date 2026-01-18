<?php

declare(strict_types=1);

use App\Models\Server;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Starter\ServerDocumentation\Enums\DocumentType;
use Starter\ServerDocumentation\Models\Document;
use Starter\ServerDocumentation\Services\DocumentService;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = app(DocumentService::class);
});

describe('DocumentService', function () {
    describe('getAllowedTypesForUser', function () {
        it('returns all types for root admin', function () {
            $user = User::factory()->create();
            $user->assignRole('root_admin');
            $server = Server::factory()->create();

            $types = $this->service->getAllowedTypesForUser($user, $server);

            expect($types)->toContain('host_admin');
            expect($types)->toContain('server_admin');
            expect($types)->toContain('server_mod');
            expect($types)->toContain('player');
        });

        it('returns player only for null user', function () {
            $server = Server::factory()->create();

            $types = $this->service->getAllowedTypesForUser(null, $server);

            expect($types)->toBe(['player']);
        });

        it('returns server_admin+ for server owner', function () {
            $user = User::factory()->create();
            $server = Server::factory()->create(['owner_id' => $user->id]);

            $types = $this->service->getAllowedTypesForUser($user, $server);

            expect($types)->not->toContain('host_admin');
            expect($types)->toContain('server_admin');
            expect($types)->toContain('server_mod');
            expect($types)->toContain('player');
        });

        it('returns player only for regular user', function () {
            $user = User::factory()->create();
            $server = Server::factory()->create();

            $types = $this->service->getAllowedTypesForUser($user, $server);

            expect($types)->toBe(['player']);
        });
    });

    describe('getUserHierarchyLevel', function () {
        it('returns host admin level for root admin', function () {
            $user = User::factory()->create();
            $user->assignRole('root_admin');
            $server = Server::factory()->create();

            $level = $this->service->getUserHierarchyLevel($user, $server);

            expect($level)->toBe(DocumentType::HostAdmin->hierarchyLevel());
        });

        it('returns server admin level for server owner', function () {
            $user = User::factory()->create();
            $server = Server::factory()->create(['owner_id' => $user->id]);

            $level = $this->service->getUserHierarchyLevel($user, $server);

            expect($level)->toBe(DocumentType::ServerAdmin->hierarchyLevel());
        });

        it('returns player level for regular user', function () {
            $user = User::factory()->create();
            $server = Server::factory()->create();

            $level = $this->service->getUserHierarchyLevel($user, $server);

            expect($level)->toBe(DocumentType::Player->hierarchyLevel());
        });
    });

    describe('isServerAdmin', function () {
        it('returns true for server owner', function () {
            $user = User::factory()->create();
            $server = Server::factory()->create(['owner_id' => $user->id]);

            expect($this->service->isServerAdmin($user, $server))->toBeTrue();
        });

        it('returns false for non-owner', function () {
            $user = User::factory()->create();
            $server = Server::factory()->create();

            expect($this->service->isServerAdmin($user, $server))->toBeFalse();
        });
    });

    describe('createVersion', function () {
        it('creates a version with correct version number', function () {
            $document = Document::factory()->create();

            $version = $this->service->createVersion($document, 'Test change');

            expect($version->version_number)->toBe(1);
            expect($version->change_summary)->toBe('Test change');
            expect($version->document_id)->toBe($document->id);
        });

        it('increments version number correctly', function () {
            $document = Document::factory()->create();

            $this->service->createVersion($document, 'First');
            $version2 = $this->service->createVersion($document, 'Second');

            expect($version2->version_number)->toBe(2);
        });

        it('stores original content in version', function () {
            $document = Document::factory()->create([
                'title' => 'Original Title',
                'content' => '<p>Original content</p>',
            ]);

            $version = $this->service->createVersion($document);

            expect($version->title)->toBe('Original Title');
            expect($version->content)->toBe('<p>Original content</p>');
        });
    });

    describe('restoreVersion', function () {
        it('restores document to version state', function () {
            $document = Document::factory()->create([
                'title' => 'New Title',
                'content' => '<p>New content</p>',
            ]);

            $version = $this->service->createVersion($document, 'Before change');

            $document->update([
                'title' => 'Changed Title',
                'content' => '<p>Changed content</p>',
            ]);

            $this->service->restoreVersion($document, $version);

            $document->refresh();
            expect($document->title)->toBe('New Title');
            expect($document->content)->toBe('<p>New content</p>');
        });

        it('creates a new version when restoring', function () {
            $document = Document::factory()->create();
            $version = $this->service->createVersion($document);

            $initialVersionCount = $document->versions()->count();

            $this->service->restoreVersion($document, $version);

            expect($document->versions()->count())->toBe($initialVersionCount + 1);
        });
    });

    describe('getDocumentCount', function () {
        it('returns correct count', function () {
            Document::factory()->count(5)->create();

            $count = $this->service->getDocumentCount();

            expect($count)->toBe(5);
        });

        it('returns zero for empty table', function () {
            $count = $this->service->getDocumentCount();

            expect($count)->toBe(0);
        });
    });

    describe('pruneVersions', function () {
        it('keeps only specified number of versions', function () {
            $document = Document::factory()->create();

            // Create 10 versions
            for ($i = 1; $i <= 10; $i++) {
                $this->service->createVersion($document, "Version $i");
            }

            expect($document->versions()->count())->toBe(10);

            $deleted = $this->service->pruneVersions($document, 5);

            expect($deleted)->toBe(5);
            expect($document->versions()->count())->toBe(5);
        });

        it('keeps most recent versions', function () {
            $document = Document::factory()->create();

            for ($i = 1; $i <= 5; $i++) {
                $this->service->createVersion($document, "Version $i");
            }

            $this->service->pruneVersions($document, 3);

            $remainingVersions = $document->versions()->pluck('version_number')->toArray();
            expect($remainingVersions)->toBe([5, 4, 3]);
        });

        it('does nothing when version count is below limit', function () {
            $document = Document::factory()->create();

            $this->service->createVersion($document, 'Only version');

            $deleted = $this->service->pruneVersions($document, 5);

            expect($deleted)->toBe(0);
            expect($document->versions()->count())->toBe(1);
        });
    });
});
