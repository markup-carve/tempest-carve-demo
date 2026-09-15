<?php

declare(strict_types=1);

namespace Tests;

use App\ContentSnippetRepository;
use App\DatabaseIncludeResolver;
use MarkupCarve\Carve\Transform\IncludeContext;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DatabaseIncludeResolverTest extends TestCase
{
    private DatabaseIncludeResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new DatabaseIncludeResolver(new ContentSnippetRepository());
    }

    #[Test]
    public function resolvesRootedAndRelativeSnippets(): void
    {
        $rooted = $this->resolver->resolve('/handbook/overview', new IncludeContext());
        $relative = $this->resolver->resolve('details', new IncludeContext('db:handbook/overview'));

        self::assertSame('db:handbook/overview', $rooted?->getId());
        self::assertSame('db:handbook/details', $relative?->getId());
    }

    #[Test]
    public function normalizesPathsAndRejectsEscapes(): void
    {
        $normalized = $this->resolver->resolve('./details', new IncludeContext('db:handbook/overview'));
        $escaped = $this->resolver->resolve('../../outside', new IncludeContext('db:handbook/overview'));

        self::assertSame('db:handbook/details', $normalized?->getId());
        self::assertNull($escaped);
        self::assertNull($this->resolver->resolve('/missing', new IncludeContext()));
    }

    #[Test]
    public function exposesAStableCacheScope(): void
    {
        self::assertSame(
            $this->resolver->includeCacheKey([]),
            $this->resolver->includeCacheKey([['target' => 'db:handbook/overview', 'resolved' => true]]),
        );
    }
}
