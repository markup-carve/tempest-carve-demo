<?php

declare(strict_types=1);

use App\DatabaseIncludeResolver;
use MarkupCarve\Carve\Extension\Navigation;
use MarkupCarve\Tempest\CarveConfig;
use MarkupCarve\Tempest\CarveProfile;
use Tempest\DateTime\Duration;

return new CarveConfig(
    profile: CarveProfile::Article,
    smartTypography: true,
    sourceLines: true,
    extensions: [Navigation::headingNumbers()],
    cacheEnabled: true,
    cacheExpiration: Duration::hours(1),
    includeResolver: DatabaseIncludeResolver::class,
);
