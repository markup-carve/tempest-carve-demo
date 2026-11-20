<?php

declare(strict_types=1);

namespace App;

use MarkupCarve\Tempest\CarveConfig;
use MarkupCarve\Tempest\CarveProfile;
use MarkupCarve\Tempest\CarveRenderer;
use MarkupCarve\Tempest\IncludeOptions;
use MarkupCarve\Tempest\RenderReport;
use Tempest\Router\Get;
use Tempest\Router\StaticPage;
use Tempest\View\View;

use function Tempest\View\view;

final readonly class ShowcaseController
{
    public function __construct(
        private CarveRenderer $carve,
    ) {}

    #[Get(uri: '/')]
    #[StaticPage]
    public function __invoke(): View
    {
        $document = $this->document();
        $formatsSource = Content::read('formats.crv');
        $includes = $this->includes();

        return view(
            'showcase.view.php',
            componentSource: $document,
            componentSourceEscaped: htmlspecialchars($document, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
            profiles: $this->profiles($document),
            htmlOutput: $this->carve->renderHtml($formatsSource),
            markdownOutput: $this->escaped($this->carve->renderMarkdown($formatsSource)),
            plainOutput: $this->escaped($this->carve->renderPlainText($formatsSource)),
            ansiOutput: $this->escaped(str_replace("\033", '\\e', $this->carve->renderAnsi($formatsSource))),
            report: $this->diagnostics(),
            lossReport: CarveRenderer::safe()->renderWithReport(Content::read('loss.crv')),
            includeSource: $this->escaped($includes['source']),
            includeHtml: $includes['html'],
            includeDependencies: $includes['dependencies'],
            includeWarnings: $includes['warnings'],
        );
    }

    /**
     * @return list<array{name: string, purpose: string, html: string}>
     */
    private function profiles(string $document): array
    {
        $profiles = [
            [CarveProfile::Full,    'The complete vocabulary, still escaped by safe mode.'],
            [CarveProfile::Article, 'Editorial content without raw HTML.'],
            [CarveProfile::Comment, 'User content with constrained structure and links.'],
            [CarveProfile::Minimal, 'Short-form text with basic inline formatting.'],
        ];
        $rendered = [];

        foreach ($profiles as [$profile, $purpose]) {
            $renderer = CarveRenderer::safe(new CarveConfig(profile: $profile));
            $rendered[] = [
                'name' => $profile->value,
                'purpose' => $purpose,
                'html' => $renderer->render($document),
            ];
        }

        return $rendered;
    }

    private function diagnostics(): RenderReport
    {
        $renderer = CarveRenderer::safe(new CarveConfig(profile: CarveProfile::Comment));

        return $renderer->renderWithReport(Content::read('diagnostics.crv'));
    }

    private function document(): string
    {
        return Content::read('showcase.crv');
    }

    private function escaped(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * @return array{source: string, html: string, dependencies: list<string>, warnings: int}
     */
    private function includes(): array
    {
        $source = Content::read('includes.crv');
        $result = $this->carve->renderIncluded(
            $source,
            new IncludeOptions(currentPath: 'db:handbook/root'),
        );

        return [
            'source' => $source,
            'html' => $result->html,
            'dependencies' => array_map(
                static fn (array $dependency): string => $dependency['target'],
                $result->dependencies,
            ),
            'warnings' => count($result->warnings),
        ];
    }
}
