<?php

declare(strict_types=1);

use App\Models\Server;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Starter\ServerDocumentation\Models\Document;

uses(RefreshDatabase::class);

describe('DocumentPolicy', function () {
    describe('admin panel permissions', function () {
        it('allows root admin all permissions', function () {
            $user = User::factory()->create();
            $user->assignRole('root_admin');
            $document = Document::factory()->create();

            expect($user->can('viewList document'))->toBeTrue();
            expect($user->can('view document'))->toBeTrue();
            expect($user->can('create document'))->toBeTrue();
            expect($user->can('update document'))->toBeTrue();
            expect($user->can('delete document'))->toBeTrue();
        });

        it('allows server admins to manage documents by default', function () {
            $user = User::factory()->create();
            // Give user server management permission
            $user->givePermissionTo('update server');

            expect($user->can('viewList document'))->toBeTrue();
            expect($user->can('create document'))->toBeTrue();
        });

        it('denies regular users when explicit_permissions is true', function () {
            config(['server-documentation.explicit_permissions' => true]);

            $user = User::factory()->create();

            expect($user->can('viewList document'))->toBeFalse();
            expect($user->can('create document'))->toBeFalse();
        });
    });

    describe('viewOnServer', function () {
        it('allows root admin to view any document including unpublished', function () {
            $user = User::factory()->create();
            $user->assignRole('root_admin');
            $server = Server::factory()->create();
            $document = Document::factory()->unpublished()->global()->create();

            expect($user->can('viewOnServer', [$document, $server]))->toBeTrue();
        });

        it('denies non-root users access to unpublished documents', function () {
            $user = User::factory()->create();
            $server = Server::factory()->create(['owner_id' => $user->id]);
            $document = Document::factory()->unpublished()->global()->serverAdmin()->create();

            expect($user->can('viewOnServer', [$document, $server]))->toBeFalse();
        });

        it('denies access to documents not associated with server', function () {
            $user = User::factory()->create();
            $server = Server::factory()->create(['owner_id' => $user->id]);
            $document = Document::factory()->published()->notGlobal()->player()->create();
            // Document not attached to server and not global

            expect($user->can('viewOnServer', [$document, $server]))->toBeFalse();
        });

        it('allows access to global published documents', function () {
            $user = User::factory()->create();
            $server = Server::factory()->create(['owner_id' => $user->id]);
            $document = Document::factory()->published()->global()->player()->create();

            expect($user->can('viewOnServer', [$document, $server]))->toBeTrue();
        });

        it('allows server owner to view server_admin documents', function () {
            $user = User::factory()->create();
            $server = Server::factory()->create(['owner_id' => $user->id]);
            $document = Document::factory()->published()->global()->serverAdmin()->create();

            expect($user->can('viewOnServer', [$document, $server]))->toBeTrue();
        });

        it('denies server owner access to host_admin documents', function () {
            $user = User::factory()->create();
            $server = Server::factory()->create(['owner_id' => $user->id]);
            $document = Document::factory()->published()->global()->hostAdmin()->create();

            expect($user->can('viewOnServer', [$document, $server]))->toBeFalse();
        });

        it('allows player access to player documents only', function () {
            $user = User::factory()->create();
            $server = Server::factory()->create(); // User is not owner

            $playerDoc = Document::factory()->published()->global()->player()->create();
            $serverModDoc = Document::factory()->published()->global()->serverMod()->create();

            expect($user->can('viewOnServer', [$playerDoc, $server]))->toBeTrue();
            expect($user->can('viewOnServer', [$serverModDoc, $server]))->toBeFalse();
        });

        it('allows access to documents attached to server', function () {
            $user = User::factory()->create();
            $server = Server::factory()->create(['owner_id' => $user->id]);
            $document = Document::factory()->published()->notGlobal()->player()->create();
            $document->servers()->attach($server);

            expect($user->can('viewOnServer', [$document, $server]))->toBeTrue();
        });
    });
});
