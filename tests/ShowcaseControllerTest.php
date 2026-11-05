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

    #[Test]
    public function index_shows_the_continuation_marker_and_a_csv_table(): void
    {
        $this->http
            ->get('/')
            ->assertOk()
            ->assertSee('<td>Plain text, ANSI</td>')
            ->assertNotSee('<p>+</p>');
    }

    #[Test]
    public function index_shows_braced_alignment_and_a_folded_comment_line(): void
    {
        $this->http
            ->get('/')
            ->assertOk()
            ->assertSee('<th scope="col" style="text-align: right;">Engines</th>')
            ->assertSee('<td style="text-align: center;">new</td>')
            ->assertSee('A comment line between two source lines keeps them in one paragraph.')
            ->assertNotSee('<td>={');
    }

    #[Test]
    public function index_includes_a_csv_record_range_from_the_database(): void
    {
        $this->http
            ->get('/')
            ->assertOk()
            ->assertSee('db:handbook/targets.csv')
            ->assertSee('<td>Terminal colors</td>')
            ->assertNotSee('<td>Markdown</td>')
            ->assertNotSee('<td>0.1.2</td>');
    }
}
