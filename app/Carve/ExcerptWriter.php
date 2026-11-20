<?php

declare(strict_types=1);

namespace App\Carve;

use MarkupCarve\Carve\Carve;
use MarkupCarve\Carve\Node;
use MarkupCarve\Carve\Output\OutputWriter;

/**
 * A host `text` writer: `renderPlainText()` uses it instead of the engine's `plain` export.
 */
final readonly class ExcerptWriter implements OutputWriter
{
    public function __construct(
        private int $limit = 80,
    ) {}

    public function target(): string
    {
        return 'text';
    }

    public function write(Node $tree, array $options = []): string
    {
        $text = trim((string) preg_replace('/\s+/', ' ', implode(' ', $this->blocks($tree))));

        return mb_strlen($text) > $this->limit ? rtrim(mb_substr($text, 0, $this->limit - 1)) . '…' : $text;
    }

    /**
     * @return list<string>
     */
    private function blocks(Node $node): array
    {
        $texts = [];
        foreach ($node->children ?? [] as $child) {
            array_push($texts, ...$child->type === 'section' ? $this->blocks($child) : [Carve::plainText([$child])]);
        }

        return $texts;
    }
}
