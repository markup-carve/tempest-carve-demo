<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\Attributes\Test;

/**
 * @internal
 */
final class CodeLinesControllerTest extends IntegrationTestCase
{
    #[Test]
    public function renders_line_numbers_and_highlighted_lines(): void
    {
        $this->http
            ->get('/code-lines')
            ->assertOk()
            ->assertSee('<pre title="src/Invoice.php" class="line-numbers" data-line-start="40" data-highlight="3,6-7"')
            ->assertSee('<span class="hl-gutter">40</span>')
            ->assertSee('<span class="hl-line-highlighted"')
            ->assertSee('<pre data-highlight="2"')
            ->assertSee('<span class="hl-gutter">1</span>');
    }
}
