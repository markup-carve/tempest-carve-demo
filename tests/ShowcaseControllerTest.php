<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\Attributes\Test;

/**
 * @internal
 */
final class ShowcaseControllerTest extends IntegrationTestCase
{
    #[Test]
    public function index_is_reachable(): void
    {
        $this->http
            ->get('/')
            ->assertOk()
            ->assertSee('One source.')
            ->assertSee('Publishing profiles')
            ->assertSee('Authoring diagnostics')
            ->assertSee('data-copy-section-link')
            ->assertSee('Copy link to Compose documents from reusable sources')
            ->assertSee('Database-backed includes')
            ->assertSee('Nested database snippet')
            ->assertSee('1.1.1</span> Nested database snippet</h3>')
            ->assertSee('db:handbook/overview')
            ->assertSee('db:handbook/details')
            ->assertSee('0 warnings')
            ->assertSee('https://markup-carve.github.io/carve/')
            ->assertSee('https://github.com/markup-carve/tempest-carve')
            ->assertSee('data-source-line')
            ->assertSee('# Portable output')
            ->assertSee('L3:C19')
            ->assertNotSee('{{ $');
    }
}
