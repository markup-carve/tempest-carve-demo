<?php

declare(strict_types=1);

namespace Tests;

use App\ContentSnippetRepository;
use App\DatabaseIncludeResolver;
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
        $rooted = $this->resolver->resolve(['path' => '/handbook/overview', 'options' => [], 'budget' => 1000]);
        $relative = $this->resolver->resolve(['path' => 'details', 'options' => [], 'from' => 'db:handbook/overview', 'budget' => 1000]);

        self::assertSame('db:handbook/overview', $rooted['id'] ?? null);
        self::assertStringContainsString('= Database chapter', $rooted['text'] ?? '');
        self::assertSame('db:handbook/details', $relative['id'] ?? null);
    }

    #[Test]
    public function normalizesPathsAndRejectsEscapes(): void
    {
        $normalized = $this->resolver->resolve(['path' => './details', 'options' => [], 'from' => 'db:handbook/overview', 'budget' => 1000]);
        $escaped = $this->resolver->resolve(['path' => '../../outside', 'options' => [], 'from' => 'db:handbook/overview', 'budget' => 1000]);

        self::assertSame('db:handbook/details', $normalized['id'] ?? null);
        self::assertSame(['error' => 'outside_root'], $escaped);
        self::assertSame(['error' => 'not_found'], $this->resolver->resolve(['path' => '/missing', 'options' => [], 'budget' => 1000]));
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
