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
            ->assertSee('data-source-line')
            ->assertSee('# Portable output')
            ->assertSee('L3:C19')
            ->assertNotSee('{{');
    }
}
