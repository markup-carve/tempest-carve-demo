<?php

declare(strict_types=1);

namespace App;

use MarkupCarve\Tempest\CarveProfile;
use MarkupCarve\Tempest\Validation\ValidCarve;

final class CommentForm
{
    #[ValidCarve(profile: CarveProfile::Comment, lint: true, maxLength: 120, message: 'The comment is not accepted: {error}')]
    public string $body;
}
