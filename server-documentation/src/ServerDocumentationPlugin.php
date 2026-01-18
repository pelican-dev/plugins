<?php

declare(strict_types=1);

namespace Starter\ServerDocumentation;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Starter\ServerDocumentation\Filament\Admin\Resources\DocumentResource;
use Starter\ServerDocumentation\Filament\Server\Pages\Documents;

class ServerDocumentationPlugin implements Plugin
{
    public function getId(): string
    {
        return 'server-documentation';
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function register(Panel $panel): void
    {
        if ($panel->getId() === 'admin') {
            $panel->resources([
                DocumentResource::class,
            ]);
        }

        if ($panel->getId() === 'server') {
            $panel->pages([
                Documents::class,
            ]);
        }
    }

    public function boot(Panel $panel): void {}
}
