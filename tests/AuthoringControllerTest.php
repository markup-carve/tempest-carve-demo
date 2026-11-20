<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\Attributes\Test;

/**
 * @internal
 */
final class AuthoringControllerTest extends IntegrationTestCase
{
    #[Test]
    public function shows_lint_and_format_results(): void
    {
        $this->http
            ->get('/authoring')
            ->assertOk()
            ->assertSee('L1:C15 deprecated-bare-underline')
            ->assertSee('L5:C1 include-no-resolver')
            ->assertSee('L5:C1 include-not-found')
            ->assertSee("|= Step             |= Owner      |\n| Tag the release   | maintainers |");
    }

    #[Test]
    public function shows_html_and_markdown_import(): void
    {
        $this->http
            ->get('/authoring')
            ->assertOk()
            ->assertSee('= Imported page')
            ->assertSee('= Imported Markdown')
            ->assertSee('Some /emphasis/, *strong text* and a [link](https://tempestphp.com).');
    }

    #[Test]
    public function validates_comments_and_maps_a_model_property(): void
    {
        $this->http
            ->get('/authoring')
            ->assertOk()
            ->assertSee('<td class="valid">Valid</td>')
            ->assertSee('The comment is not accepted: markup is not allowed by the comment profile')
            ->assertSee('The comment is not accepted: must not exceed 120 characters')
            ->assertSee('The comment is not accepted: A bare underscore pair is text in 0.2.')
            ->assertSee('&lt;em&gt;new&lt;/em&gt; version is out.')
            ->assertSee('{&quot;title&quot;:&quot;Release day&quot;,&quot;body&quot;:&quot;= Release day');
    }

    #[Test]
    public function shows_strict_losses_and_include_limits(): void
    {
        $this->http
            ->get('/authoring')
            ->assertOk()
            ->assertSee('RenderError: the render has a loss under strictLosses')
            ->assertSee('include-depth: Include &quot;details&quot; was not expanded')
            ->assertSee('include-budget:')
            ->assertSee('<td>2</td>');
    }
}
