<?php

declare(strict_types=1);

namespace Starter\ServerDocumentation\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Starter\ServerDocumentation\Enums\DocumentType;
use Starter\ServerDocumentation\Models\Document;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    protected $model = Document::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'title' => fake()->sentence(4),
            'slug' => fake()->unique()->slug(3),
            'content' => '<p>' . fake()->paragraphs(3, true) . '</p>',
            'type' => fake()->randomElement(DocumentType::cases())->value,
            'is_global' => fake()->boolean(20),
            'is_published' => fake()->boolean(80),
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }

    /**
     * Document with host_admin type (Root Admins only).
     */
    public function hostAdmin(): static
    {
        return $this->state(['type' => DocumentType::HostAdmin->value]);
    }

    /**
     * Document with server_admin type.
     */
    public function serverAdmin(): static
    {
        return $this->state(['type' => DocumentType::ServerAdmin->value]);
    }

    /**
     * Document with server_mod type.
     */
    public function serverMod(): static
    {
        return $this->state(['type' => DocumentType::ServerMod->value]);
    }

    /**
     * Document with player type (visible to all).
     */
    public function player(): static
    {
        return $this->state(['type' => DocumentType::Player->value]);
    }

    /**
     * Published document.
     */
    public function published(): static
    {
        return $this->state(['is_published' => true]);
    }

    /**
     * Unpublished/draft document.
     */
    public function unpublished(): static
    {
        return $this->state(['is_published' => false]);
    }

    /**
     * Global document (visible on all servers).
     */
    public function global(): static
    {
        return $this->state(['is_global' => true]);
    }

    /**
     * Non-global document (must be attached to servers).
     */
    public function notGlobal(): static
    {
        return $this->state(['is_global' => false]);
    }
}
