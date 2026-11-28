<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\Attributes\Test;

/**
 * @internal
 */
final class ConsoleControllerTest extends IntegrationTestCase
{
    #[Test]
    public function page_shows_each_command_run(): void
    {
        $this->process->allowRunningActualProcesses();

        $this->http
            ->get('/console')
            ->assertOk()
            ->assertSee('$ php tempest carve:render content/formats.crv --format=markdown <span class="exit-code">exit 0</span>')
            ->assertSee('Portable output One source can serve many targets.')
            ->assertSee('part.crv:3:8  markdown-strong-double-star')
            ->assertSee('| Publish the notes | docs team   |')
            ->assertSee('$ php tempest carve:fmt content/showcase.crv --check <span class="exit-code">exit 0</span>')
            ->assertSee('= Imported Markdown');
        $this->process->assertCommandRan("*'carve:convert' 'content/samples/import.md' '--from=markdown'");
    }

    #[Test]
    public function commands_run_on_the_demo_content(): void
    {
        $this->console->call('carve:render content/formats.crv --format=ansi')->assertSuccess()->assertSee('Portable output');
        $this->console->call('carve:lint content/samples/lint.crv')->assertError()->assertSee('markdown-strong-double-star');
        $this->console->call('carve:fmt content/samples/unformatted.crv --check')->assertError()->assertSee('is not formatted');
        $this->console->call('carve:convert content/samples/import.html')->assertSuccess()->assertSee('= Imported page');
    }
}
