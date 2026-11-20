<?php

declare(strict_types=1);

use App\Carve\DisclosureExtensions;
use App\Carve\ExcerptWriter;
use App\DatabaseIncludeResolver;
use MarkupCarve\Carve\Extension\Navigation;
use MarkupCarve\Tempest\CarveConfig;
use MarkupCarve\Tempest\CarveProfile;
use Tempest\DateTime\Duration;

return new CarveConfig(
    profile: CarveProfile::Article,
    smartTypography: true,
    sourceLines: true,
    headingIds: 'keep-case',
    mode: 'interactive',
    sections: true,
    rawHtml: false,
    extensions: [
        Navigation::headingNumbers(),
        DisclosureExtensions::class,
        'tabs',
        'mermaid',
        'plantuml',
        ['type' => 'fenced_render', 'language' => 'graphviz'],
        ['type' => 'wikilinks', 'url_template' => 'https://en.wikipedia.org/wiki/{page}'],
        'tempest_highlight',
    ],
    cacheEnabled: true,
    cacheExpiration: Duration::hours(1),
    cacheStore: null,
    // Navigation::headingNumbers() holds closures, so the cache key needs an explicit salt.
    cacheKeySalt: 'tempest-carve-demo-1',
    namedRenderers: [
        'comments' => new CarveConfig(profile: CarveProfile::Comment, smartTypography: false, headingIds: 'lower'),
        'static' => new CarveConfig(mode: 'static', sections: false, headingIds: 'ascii-lower', extensions: [DisclosureExtensions::class]),
        'trusted' => new CarveConfig(rawHtml: true, urlPolicy: ['allow' => ['https', 'mailto']]),
        'excerpt' => new CarveConfig(writers: [new ExcerptWriter()]),
    ],
    includeResolver: DatabaseIncludeResolver::class,
);
