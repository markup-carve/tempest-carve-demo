<?php

declare(strict_types=1);

namespace App;

use RuntimeException;

final class Content
{
    public const string ROOT = __DIR__ . '/../content';

    public static function path(string $name): string
    {
        return self::ROOT . '/' . $name;
    }

    public static function read(string $name): string
    {
        $source = file_get_contents(self::path($name));
        if ($source === false) {
            throw new RuntimeException(sprintf('Could not read the demo content %s.', $name));
        }

        return $source;
    }
}
