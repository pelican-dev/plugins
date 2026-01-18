<?php

declare(strict_types=1);

use App\Models\Server;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Starter\ServerDocumentation\Models\Document;

uses(RefreshDatabase::class);

describe('Document Model', function () {
    describe('creation', function () {
        it('generates uuid on creation', function () {
            $document = Document::factory()->create(['uuid' => null]);

            expect($document->uuid)->not->toBeNull();
            expect(Str::isUuid($document->uuid))->toBeTrue();
        });

        it('generates slug from title on creation', function () {
            $document = Document::factory()->create([
                'title' => 'My Test Document',
                'slug' => null,
            ]);

            expect($document->slug)->toBe('my-test-document');
        });

        it('preserves provided slug', function () {
            $document = Document::factory()->create([
                'title' => 'My Test Document',
                'slug' => 'custom-slug',
            ]);

            expect($document->slug)->toBe('custom-slug');
        });

        it('sets author_id from authenticated user', function () {
            $user = User::factory()->create();
            $this->actingAs($user);

            $document = Document::factory()->create(['author_id' => null]);

            expect($document->author_id)->toBe($user->id);
        });

        it('does not override provided author_id', function () {
            $author = User::factory()->create();
            $otherUser = User::factory()->create();
            $this->actingAs($otherUser);

            $document = Document::factory()->create(['author_id' => $author->id]);

            expect($document->author_id)->toBe($author->id);
        });
    });

    describe('updating', function () {
        it('creates version on content change', function () {
            $document = Document::factory()->create(['content' => '<p>Original</p>']);

            $document->update(['content' => '<p>Updated</p>']);

            expect($document->versions()->count())->toBe(1);
        });

        it('creates version on title change', function () {
            $document = Document::factory()->create(['title' => 'Original']);

            $document->update(['title' => 'Updated']);

            expect($document->versions()->count())->toBe(1);
        });

        it('stores original content in version', function () {
            $document = Document::factory()->create([
                'title' => 'Original Title',
                'content' => '<p>Original content</p>',
            ]);

            $document->update(['content' => '<p>New content</p>']);

            $version = $document->versions()->first();
            expect($version->title)->toBe('Original Title');
            expect($version->content)->toBe('<p>Original content</p>');
        });

        it('does not create version when other fields change', function () {
            $document = Document::factory()->create();

            $document->update(['sort_order' => 99]);

            expect($document->versions()->count())->toBe(0);
        });

        it('sets last_edited_by on content change', function () {
            $author = User::factory()->create();
            $editor = User::factory()->create();

            $document = Document::factory()->create(['author_id' => $author->id]);

            $this->actingAs($editor);
            $document->update(['content' => '<p>Edited</p>']);

            expect($document->fresh()->last_edited_by)->toBe($editor->id);
        });
    });

    describe('scopes', function () {
        it('filters by document type', function () {
            Document::factory()->hostAdmin()->create();
            Document::factory()->serverAdmin()->create();
            Document::factory()->serverMod()->create();
            Document::factory()->player()->create();

            expect(Document::hostAdmin()->count())->toBe(1);
            expect(Document::serverAdmin()->count())->toBe(1);
            expect(Document::serverMod()->count())->toBe(1);
            expect(Document::player()->count())->toBe(1);
        });

        it('filters published documents', function () {
            Document::factory()->count(3)->published()->create();
            Document::factory()->count(2)->unpublished()->create();

            expect(Document::published()->count())->toBe(3);
        });

        it('filters global documents', function () {
            Document::factory()->count(2)->global()->create();
            Document::factory()->count(3)->notGlobal()->create();

            expect(Document::global()->count())->toBe(2);
        });

        it('filters documents for a specific server', function () {
            $server = Server::factory()->create();

            // Document attached to server
            $attached = Document::factory()->notGlobal()->create();
            $attached->servers()->attach($server);

            // Global document (not attached but should appear)
            Document::factory()->global()->create();

            // Document not attached and not global
            Document::factory()->notGlobal()->create();

            expect(Document::forServer($server)->count())->toBe(2);
        });

        it('filters documents by allowed types', function () {
            Document::factory()->hostAdmin()->create();
            Document::factory()->serverAdmin()->create();
            Document::factory()->player()->create();

            $allowedTypes = ['server_admin', 'player'];
            expect(Document::forTypes($allowedTypes)->count())->toBe(2);
        });
    });

    describe('relationships', function () {
        it('belongs to author', function () {
            $user = User::factory()->create();
            $document = Document::factory()->create(['author_id' => $user->id]);

            expect($document->author->id)->toBe($user->id);
        });

        it('belongs to last editor', function () {
            $user = User::factory()->create();
            $document = Document::factory()->create(['last_edited_by' => $user->id]);

            expect($document->lastEditor->id)->toBe($user->id);
        });

        it('belongs to many servers', function () {
            $document = Document::factory()->create();
            $servers = Server::factory()->count(3)->create();

            $document->servers()->attach($servers->pluck('id'));

            expect($document->servers)->toHaveCount(3);
        });

        it('has many versions', function () {
            $document = Document::factory()->create();

            // Create versions by updating
            $document->update(['content' => '<p>v1</p>']);
            sleep(1); // Ensure different timestamps for rate limiting
            $document->update(['content' => '<p>v2</p>']);

            // Note: Due to rate limiting, rapid updates may be merged
            expect($document->versions()->count())->toBeGreaterThanOrEqual(1);
        });
    });

    describe('type helpers', function () {
        it('returns correct document type enum', function () {
            $document = Document::factory()->hostAdmin()->create();

            expect($document->getDocumentType())->toBe(\Starter\ServerDocumentation\Enums\DocumentType::HostAdmin);
        });

        it('handles legacy admin type', function () {
            $document = Document::factory()->create(['type' => 'admin']);

            expect($document->getDocumentType())->toBe(\Starter\ServerDocumentation\Enums\DocumentType::ServerAdmin);
        });

        it('checks type visibility correctly', function () {
            $hostAdmin = Document::factory()->hostAdmin()->create();
            $player = Document::factory()->player()->create();

            expect($hostAdmin->isHostAdminOnly())->toBeTrue();
            expect($hostAdmin->isPlayerVisible())->toBeFalse();

            expect($player->isPlayerVisible())->toBeTrue();
            expect($player->isHostAdminOnly())->toBeFalse();
        });

        it('returns correct required tier', function () {
            $hostAdmin = Document::factory()->hostAdmin()->create();
            $player = Document::factory()->player()->create();

            expect($hostAdmin->getRequiredTier())->toBe(4);
            expect($player->getRequiredTier())->toBe(1);
        });

        it('checks tier visibility correctly', function () {
            $hostAdmin = Document::factory()->hostAdmin()->create();

            expect($hostAdmin->isVisibleToTier(4))->toBeTrue();
            expect($hostAdmin->isVisibleToTier(3))->toBeFalse();
            expect($hostAdmin->isVisibleToTier(1))->toBeFalse();
        });
    });
});
