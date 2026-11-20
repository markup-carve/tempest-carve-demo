<?php

declare(strict_types=1);

namespace App;

use MarkupCarve\Tempest\Mapper\CarveContent;

final class Post
{
    public string $title;

    public CarveContent $body;
}
