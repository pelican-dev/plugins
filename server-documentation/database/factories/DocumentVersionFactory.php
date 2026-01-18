<?php

declare(strict_types=1);

namespace Starter\ServerDocumentation\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Starter\ServerDocumentation\Models\Document;
use Starter\ServerDocumentation\Models\DocumentVersion;

/**
 * @extends Factory<DocumentVersion>
 */
class DocumentVersionFactory extends Factory
{
    protected $model = DocumentVersion::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'document_id' => Document::factory(),
            'title' => fake()->sentence(4),
            'content' => '<p>' . fake()->paragraphs(2, true) . '</p>',
            'version_number' => 1,
            'change_summary' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Create a version with a specific version number.
     */
    public function versionNumber(int $number): static
    {
        return $this->state(['version_number' => $number]);
    }

    /**
     * Create a version for a specific document.
     */
    public function forDocument(Document $document): static
    {
        return $this->state(['document_id' => $document->id]);
    }
}
