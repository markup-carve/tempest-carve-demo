<?php

declare(strict_types=1);

namespace App\Carve;

use MarkupCarve\Carve\Extension\Bundle;
use MarkupCarve\Carve\Extension\Details;
use MarkupCarve\Carve\Extension\ExtensionProvider;
use MarkupCarve\Carve\Extension\Spoiler;

/**
 * Resolved from the container by its class name in `extensions`.
 */
final readonly class DisclosureExtensions implements ExtensionProvider
{
    public function definitions(): array
    {
        return Bundle::merge(Details::bundle(), Spoiler::bundle())->definitions();
    }
}
