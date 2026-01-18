<?php

declare(strict_types=1);

use Starter\ServerDocumentation\Enums\DocumentType;

describe('DocumentType Enum', function () {
    it('has correct hierarchy levels', function () {
        expect(DocumentType::HostAdmin->hierarchyLevel())->toBe(4);
        expect(DocumentType::ServerAdmin->hierarchyLevel())->toBe(3);
        expect(DocumentType::ServerMod->hierarchyLevel())->toBe(2);
        expect(DocumentType::Player->hierarchyLevel())->toBe(1);
    });

    it('returns correct labels', function () {
        expect(DocumentType::HostAdmin->label())->toBeString();
        expect(DocumentType::ServerAdmin->label())->toBeString();
        expect(DocumentType::ServerMod->label())->toBeString();
        expect(DocumentType::Player->label())->toBeString();
    });

    it('returns correct colors', function () {
        expect(DocumentType::HostAdmin->color())->toBe('danger');
        expect(DocumentType::ServerAdmin->color())->toBe('warning');
        expect(DocumentType::ServerMod->color())->toBe('info');
        expect(DocumentType::Player->color())->toBe('success');
    });

    it('returns correct icons', function () {
        expect(DocumentType::HostAdmin->icon())->toBe('tabler-shield-lock');
        expect(DocumentType::ServerAdmin->icon())->toBe('tabler-lock');
        expect(DocumentType::ServerMod->icon())->toBe('tabler-user-shield');
        expect(DocumentType::Player->icon())->toBe('tabler-file-text');
    });

    it('handles legacy admin type', function () {
        $type = DocumentType::tryFromLegacy('admin');

        expect($type)->toBe(DocumentType::ServerAdmin);
    });

    it('validates type strings correctly', function () {
        expect(DocumentType::isValid('host_admin'))->toBeTrue();
        expect(DocumentType::isValid('server_admin'))->toBeTrue();
        expect(DocumentType::isValid('server_mod'))->toBeTrue();
        expect(DocumentType::isValid('player'))->toBeTrue();
        expect(DocumentType::isValid('admin'))->toBeTrue(); // Legacy
        expect(DocumentType::isValid('invalid'))->toBeFalse();
        expect(DocumentType::isValid(''))->toBeFalse();
    });

    it('returns types visible to each hierarchy level', function () {
        $hostAdminTypes = DocumentType::typesVisibleToLevel(4);
        expect($hostAdminTypes)->toContain('host_admin');
        expect($hostAdminTypes)->toContain('server_admin');
        expect($hostAdminTypes)->toContain('server_mod');
        expect($hostAdminTypes)->toContain('player');
        expect($hostAdminTypes)->toContain('admin'); // Legacy

        $serverAdminTypes = DocumentType::typesVisibleToLevel(3);
        expect($serverAdminTypes)->not->toContain('host_admin');
        expect($serverAdminTypes)->toContain('server_admin');
        expect($serverAdminTypes)->toContain('server_mod');
        expect($serverAdminTypes)->toContain('player');

        $serverModTypes = DocumentType::typesVisibleToLevel(2);
        expect($serverModTypes)->not->toContain('host_admin');
        expect($serverModTypes)->not->toContain('server_admin');
        expect($serverModTypes)->toContain('server_mod');
        expect($serverModTypes)->toContain('player');

        $playerTypes = DocumentType::typesVisibleToLevel(1);
        expect($playerTypes)->toBe(['player']);
    });

    it('checks visibility to tier correctly', function () {
        expect(DocumentType::HostAdmin->isVisibleToLevel(4))->toBeTrue();
        expect(DocumentType::HostAdmin->isVisibleToLevel(3))->toBeFalse();

        expect(DocumentType::ServerAdmin->isVisibleToLevel(3))->toBeTrue();
        expect(DocumentType::ServerAdmin->isVisibleToLevel(2))->toBeFalse();

        expect(DocumentType::Player->isVisibleToLevel(1))->toBeTrue();
    });

    it('provides options for form selects', function () {
        $options = DocumentType::options();

        expect($options)->toBeArray();
        expect($options)->toHaveCount(4);
        expect(array_keys($options))->toBe(['host_admin', 'server_admin', 'server_mod', 'player']);
    });

    it('provides simple options without descriptions', function () {
        $options = DocumentType::simpleOptions();

        expect($options)->toBeArray();
        expect($options)->toHaveCount(4);
    });

    it('formats labels for unknown types gracefully', function () {
        expect(DocumentType::formatLabel('unknown'))->toBe('unknown');
        expect(DocumentType::formatColor('unknown'))->toBe('gray');
        expect(DocumentType::formatIcon('unknown'))->toBe('tabler-file-text');
    });
});
