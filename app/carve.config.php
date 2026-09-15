<?php

declare(strict_types=1);

use App\DatabaseIncludeResolver;
use MarkupCarve\Carve\Extension\HeadingNumbersExtension;
use MarkupCarve\Carve\Renderer\SmartTypographyMode;
use MarkupCarve\Carve\Renderer\SoftBreakMode;
use MarkupCarve\Tempest\CarveConfig;
use MarkupCarve\Tempest\CarveProfile;
use Tempest\DateTime\Duration;

return new CarveConfig(
    profile: CarveProfile::Article,
    softBreakMode: SoftBreakMode::Space,
    smartTypography: SmartTypographyMode::Glyph,
    sourceLines: true,
    extensions: [HeadingNumbersExtension::class],
    cacheEnabled: true,
    cacheExpiration: Duration::hours(1),
    includeResolver: DatabaseIncludeResolver::class,
);
