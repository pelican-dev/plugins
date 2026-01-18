<?php

declare(strict_types=1);

use Starter\ServerDocumentation\Services\MarkdownConverter;

beforeEach(function () {
    $this->converter = new MarkdownConverter();
});

describe('MarkdownConverter', function () {
    describe('toHtml', function () {
        it('converts basic markdown to html', function () {
            $markdown = "# Hello World\n\nThis is a paragraph.";
            $html = $this->converter->toHtml($markdown);

            expect($html)->toContain('<h1>');
            expect($html)->toContain('Hello World');
            expect($html)->toContain('<p>');
        });

        it('sanitizes dangerous html by default', function () {
            $markdown = '<script>alert("xss")</script>Hello';
            $html = $this->converter->toHtml($markdown);

            expect($html)->not->toContain('<script>');
            expect($html)->not->toContain('alert');
        });

        it('can skip sanitization when explicitly requested', function () {
            $markdown = '**bold**';
            $html = $this->converter->toHtml($markdown, sanitize: false);

            expect($html)->toContain('<strong>bold</strong>');
        });
    });

    describe('toMarkdown', function () {
        it('converts html headings to markdown', function () {
            $html = '<h1>Title</h1><h2>Subtitle</h2>';
            $markdown = $this->converter->toMarkdown($html);

            expect($markdown)->toContain('# Title');
            expect($markdown)->toContain('## Subtitle');
        });

        it('converts html formatting to markdown', function () {
            $html = '<strong>bold</strong> and <em>italic</em>';
            $markdown = $this->converter->toMarkdown($html);

            expect($markdown)->toContain('**bold**');
            expect($markdown)->toContain('*italic*');
        });

        it('converts html links to markdown', function () {
            $html = '<a href="https://example.com">Link Text</a>';
            $markdown = $this->converter->toMarkdown($html);

            expect($markdown)->toContain('[Link Text](https://example.com)');
        });

        it('converts html lists to markdown', function () {
            $html = '<ul><li>Item 1</li><li>Item 2</li></ul>';
            $markdown = $this->converter->toMarkdown($html);

            expect($markdown)->toContain('- Item 1');
            expect($markdown)->toContain('- Item 2');
        });

        it('strips script tags from html', function () {
            $html = '<p>Hello</p><script>alert("xss")</script>';
            $markdown = $this->converter->toMarkdown($html);

            expect($markdown)->not->toContain('script');
            expect($markdown)->not->toContain('alert');
        });
    });

    describe('sanitizeHtml', function () {
        it('removes script tags', function () {
            $html = '<p>Hello</p><script>alert("xss")</script>';
            $sanitized = $this->converter->sanitizeHtml($html);

            expect($sanitized)->not->toContain('<script>');
        });

        it('removes event handlers', function () {
            $html = '<img src="x" onerror="alert(1)">';
            $sanitized = $this->converter->sanitizeHtml($html);

            expect($sanitized)->not->toContain('onerror');
        });

        it('removes javascript urls', function () {
            $html = '<a href="javascript:alert(1)">Click</a>';
            $sanitized = $this->converter->sanitizeHtml($html);

            expect($sanitized)->not->toContain('javascript:');
        });

        it('preserves safe html', function () {
            $html = '<p><strong>Bold</strong> and <em>italic</em></p>';
            $sanitized = $this->converter->sanitizeHtml($html);

            expect($sanitized)->toContain('<p>');
            expect($sanitized)->toContain('<strong>');
            expect($sanitized)->toContain('<em>');
        });
    });

    describe('frontmatter', function () {
        it('adds frontmatter to markdown', function () {
            $markdown = '# Hello';
            $metadata = ['title' => 'Test', 'type' => 'player'];
            $result = $this->converter->addFrontmatter($markdown, $metadata);

            expect($result)->toStartWith('---');
            expect($result)->toContain('title: Test');
            expect($result)->toContain('type: player');
            expect($result)->toContain('# Hello');
        });

        it('handles boolean values in frontmatter', function () {
            $metadata = ['is_published' => true, 'is_global' => false];
            $result = $this->converter->addFrontmatter('content', $metadata);

            expect($result)->toContain('is_published: true');
            expect($result)->toContain('is_global: false');
        });

        it('parses frontmatter from markdown', function () {
            $markdown = "---\ntitle: Test\ntype: player\n---\n\n# Content";
            [$metadata, $content] = $this->converter->parseFrontmatter($markdown);

            expect($metadata)->toHaveKey('title', 'Test');
            expect($metadata)->toHaveKey('type', 'player');
            expect($content)->toBe('# Content');
        });

        it('handles markdown without frontmatter', function () {
            $markdown = '# Just content';
            [$metadata, $content] = $this->converter->parseFrontmatter($markdown);

            expect($metadata)->toBe([]);
            expect($content)->toBe('# Just content');
        });

        it('parses boolean strings in frontmatter', function () {
            $markdown = "---\nis_published: true\nis_global: false\n---\n\ncontent";
            [$metadata, $content] = $this->converter->parseFrontmatter($markdown);

            expect($metadata['is_published'])->toBeTrue();
            expect($metadata['is_global'])->toBeFalse();
        });
    });

    describe('filename generation', function () {
        it('generates filename from slug', function () {
            $filename = $this->converter->generateFilename('My Title', 'my-slug');

            expect($filename)->toBe('my-slug.md');
        });

        it('generates filename from title when slug is empty', function () {
            $filename = $this->converter->generateFilename('My Title', '');

            expect($filename)->toBe('my-title.md');
        });

        it('sanitizes special characters in filename', function () {
            $filename = $this->converter->generateFilename('Test @#$% Title!', '');

            expect($filename)->toBe('test-title.md');
        });
    });
});
