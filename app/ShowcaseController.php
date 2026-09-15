<?php

declare(strict_types=1);

namespace App;

use MarkupCarve\Carve\CarveConverter;
use MarkupCarve\Carve\Transform\FilesystemIncludeResolver;
use MarkupCarve\Carve\Transform\IncludeDependency;
use MarkupCarve\Carve\Transform\IncludeExpander;
use MarkupCarve\Tempest\CarveConfig;
use MarkupCarve\Tempest\CarveProfile;
use MarkupCarve\Tempest\CarveRenderer;
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
        $formatsSource = "# Portable output\n\n/One source/ can serve *many targets*.";
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
            lossReport: CarveRenderer::safe()->renderWithReport("```=latex\n\\textbf{x}\n```"),
            includeSource: $this->escaped($includes['source']),
            includeHtml: $includes['html'],
            includeDependencies: $includes['dependencies'],
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

        return $renderer->renderWithReport(<<<'CARVE'
        # A heading comments cannot publish

        This reference is [missing][nowhere].

        ```=html
        <script>alert('comments cannot publish raw HTML')</script>
        ```
        CARVE);
    }

    private function document(): string
    {
        return <<<'CARVE'
        # Carve meets Tempest

        /Readable source/ becomes *safe HTML*, with _underlines_, =highlights=,
        smart typography -- and links to [Tempest](https://tempestphp.com/).

        ## Built for application content

        - Safe-by-default raw HTML handling
        - Named publishing profiles
        - Container-resolved extensions
        - Source-line annotations for editor synchronization

        > The configured renderer numbers headings and caches this output.

        ```php
        $html = $carve->render($source);
        ```

        ```=html
        <script>alert('This is escaped, never executed')</script>
        ```
        CARVE;
    }

    private function escaped(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * @return array{source: string, html: string, dependencies: list<string>}
     */
    private function includes(): array
    {
        $source = "# Composed handbook\n\n{{ chapters/overview.crv @shift:auto }}";
        $contentRoot = __DIR__ . '/content';
        $converter = new CarveConverter(safeMode: true, sourceLines: true);
        $document = $converter->parse($source);
        $expander = new IncludeExpander(
            resolver: new FilesystemIncludeResolver($contentRoot),
            currentPath: 'handbook.crv',
            source: $source,
        );
        $expanded = $converter->transform($document, $expander);

        return [
            'source' => $source,
            'html' => $converter->render($expanded),
            'dependencies' => array_map(
                static fn (IncludeDependency $dependency): string => ltrim(
                    str_replace($contentRoot, '', $dependency->getTarget()),
                    DIRECTORY_SEPARATOR,
                ),
                $expander->getDependencies(),
            ),
        ];
    }
}
