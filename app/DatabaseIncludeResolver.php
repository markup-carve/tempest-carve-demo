<?php

declare(strict_types=1);

namespace App;

use MarkupCarve\Carve\Transform\IncludeContext;
use MarkupCarve\Carve\Transform\IncludeResolverInterface;
use MarkupCarve\Carve\Transform\ResolvedInclude;
use MarkupCarve\Tempest\IncludeCacheKeyProvider;

final readonly class DatabaseIncludeResolver implements IncludeResolverInterface, IncludeCacheKeyProvider
{
    public function __construct(
        private ContentSnippetRepository $snippets,
    ) {}

    public function resolve(string $path, IncludeContext $context): ?ResolvedInclude
    {
        $slug = $this->resolveSlug($path, $context->getIncludingPath());
        if ($slug === null) {
            return null;
        }
        $snippet = $this->snippets->find($slug);

        return $snippet === null
            ? null
            : new ResolvedInclude($snippet['body'], 'db:' . $slug);
    }

    public function includeCacheKey(array $dependencies): string
    {
        return 'sqlite-demo:' . $this->snippets->revisionKey();
    }

    private function resolveSlug(string $path, ?string $includingPath): ?string
    {
        $rooted = str_starts_with($path, '/');
        $parent = '';
        if (! $rooted && $includingPath !== null) {
            $id = str_starts_with($includingPath, 'db:') ? substr($includingPath, 3) : $includingPath;
            $parent = dirname($id);
        }

        $segments = explode('/', ($rooted || $parent === '.' ? '' : $parent . '/') . ltrim($path, '/'));
        $normalized = [];
        foreach ($segments as $segment) {
            if ($segment === '' || $segment === '.') {
                continue;
            }
            if ($segment === '..') {
                if ($normalized === []) {
                    return null;
                }
                array_pop($normalized);
                continue;
            }
            $normalized[] = $segment;
        }

        return $normalized === [] ? null : implode('/', $normalized);
    }
}
