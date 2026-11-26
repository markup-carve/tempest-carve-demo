<?php

declare(strict_types=1);

namespace Tests;

use App\Content;
use MarkupCarve\Tempest\Testing\CarveAssertions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * The demo content checked with tempest-carve's PHPUnit assertions.
 *
 * @internal
 */
final class CarveAssertionsTest extends TestCase
{
    use CarveAssertions;

    #[Test]
    public function demo_content_renders_safely(): void
    {
        $this->assertCarveRenders('<em>One source</em>', Content::read('formats.crv'));
        $this->assertCarveIsSafe(Content::read('showcase.crv'), '<script>');
        $this->assertCarveHasWarning('include', Content::read('diagnostics.crv'));
    }
}
