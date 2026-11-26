<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\Attributes\Test;

/**
 * @internal
 */
final class ExtensionsControllerTest extends IntegrationTestCase
{
    #[Test]
    public function renders_every_configured_extension(): void
    {
        $this->http
            ->get('/extensions')
            ->assertOk()
            ->assertSee('<span class="section-number">1</span> Extensions in one document')
            ->assertSee('<div class="tabs" role="group" aria-label="Tabs"')
            ->assertSee('<pre class="mermaid">')
            ->assertSee('<pre class="plantuml">')
            ->assertSee('<pre class="graphviz">')
            ->assertSee('href="https://en.wikipedia.org/wiki/markup-language"')
            ->assertSee('<span class="hl-variable">$html</span>')
            ->assertSee('<summary>Why a container-resolved extension?</summary>')
            ->assertSee('<span class="spoiler">safe render</span>')
            ->assertSee('https://docs.example.org/getting-started.html')
            ->assertNotSee('{{ $');
    }

    #[Test]
    public function compares_each_rendering_option(): void
    {
        $this->http
            ->get('/extensions')
            ->assertOk()
            ->assertSee('smartTypography')
            ->assertSee('“Quotes” – dashes…')
            ->assertSee('data-source-line=&quot;1&quot;')
            ->assertSee('&lt;details open&gt;')
            ->assertSee('&lt;mark&gt;trusted&lt;/mark&gt;')
            ->assertSee('&lt;a href=&quot;&quot;&gt;plain&lt;/a&gt;')
            ->assertSee('render($source, CarveProfile::Minimal)')
            ->assertSee('Uber-Strasse-Co')
            ->assertSee('uber-strasse-co')
            ->assertSee('&uuml;ber-stra&szlig;e-co')
            ->assertSee('(no id)');
    }

    #[Test]
    public function shows_named_renderers_and_a_host_writer(): void
    {
        $this->http
            ->get('/extensions')
            ->assertOk()
            ->assertSee('profile: Comment, smartTypography: false')
            ->assertSee('&lt;h1 id=&quot;named-renderers&quot;&gt;Named renderers&lt;/h1&gt;')
            ->assertSee('&lt;mark&gt;raw&lt;/mark&gt;')
            ->assertSee('id="excerpt-output">Carve meets Tempest Readable source becomes safe HTML');
    }
}
