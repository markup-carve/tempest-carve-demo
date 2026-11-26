<?php

declare(strict_types=1);

namespace App;

use MarkupCarve\Tempest\CarveConfig;
use MarkupCarve\Tempest\CarveProfile;
use MarkupCarve\Tempest\CarveRenderer;
use MarkupCarve\Tempest\ExtensionFactory;
use MarkupCarve\Tempest\WikiLinkUrlTemplate;
use Tempest\Router\Get;
use Tempest\Router\StaticPage;
use Tempest\View\View;

use function Tempest\View\view;

final readonly class ExtensionsController
{
    private const string HEADING = '= Über Straße & Co';

    private const string DISCLOSURE = "::: details \"Release notes\"\nOnly the summary shows until it is opened.\n:::\n\nA :spoiler[hidden] word.";

    public function __construct(
        private CarveRenderer $carve,
    ) {}

    #[Get(uri: '/extensions')]
    #[StaticPage]
    public function __invoke(): View
    {
        $source = Content::read('extensions.crv');

        return view(
            'extensions.view.php',
            source: $this->escaped($source),
            html: $this->carve->render($source),
            headingIds: $this->headingIds(),
            options: $this->options(),
            named: $this->named(),
            excerpt: [
                'default' => $this->escaped($this->carve->renderPlainText(Content::read('showcase.crv'))),
                'writer' => $this->escaped($this->carve->named('excerpt')->renderPlainText(Content::read('showcase.crv'))),
            ],
            wikiGenerator: $this->wikiGenerator(),
        );
    }

    /**
     * @return list<array{value: string, id: string}>
     */
    private function headingIds(): array
    {
        $rows = [];
        foreach (['keep-case', 'lower', 'ascii', 'ascii-lower', 'ascii-strict', 'ascii-strict-lower', 'off'] as $value) {
            $match = [];
            $html = CarveRenderer::safe(new CarveConfig(headingIds: $value, sections: false))->render(self::HEADING);
            $rows[] = [
                'value' => $value,
                'id' => preg_match('/ id="([^"]*)"/', $html, $match) === 1 ? $match[1] ?? '' : '(no id)',
            ];
        }

        return $rows;
    }

    /**
     * @return list<array{option: string, source: string, before: string, after: string, beforeLabel: string, afterLabel: string}>
     */
    private function options(): array
    {
        $rows = [];
        $compare = function (string $option, string $source, CarveConfig $before, CarveConfig $after, string $beforeLabel, string $afterLabel, bool $disclosures = false) use (
            &$rows,
        ): void {
            $extensions = $disclosures ? [new Carve\DisclosureExtensions()] : [];
            $rows[] = [
                'option' => $option,
                'source' => $this->escaped($source),
                'before' => $this->escaped(CarveRenderer::safe($before, extensions: $extensions)->render($source)),
                'after' => $this->escaped(CarveRenderer::safe($after, extensions: $extensions)->render($source)),
                'beforeLabel' => $beforeLabel,
                'afterLabel' => $afterLabel,
            ];
        };
        $compare('smartTypography', '"Quotes" -- dashes...', new CarveConfig(), new CarveConfig(smartTypography: false), 'true (default)', 'false');
        $compare('sourceLines', "= Title\n\nA paragraph.", new CarveConfig(), new CarveConfig(sourceLines: true), 'false (default)', 'true');
        $compare('sections', "= Title\n\nA paragraph.", new CarveConfig(), new CarveConfig(sections: false), 'true (default)', 'false');
        $compare('mode', self::DISCLOSURE, new CarveConfig(), new CarveConfig(mode: 'static'), "'interactive' (default)", "'static'", disclosures: true);
        $compare('rawHtml', "```=html\n<mark>trusted</mark>\n```", new CarveConfig(), new CarveConfig(rawHtml: true), 'false (default)', 'true');
        $compare(
            'urlPolicy',
            '[web](https://tempestphp.com) [mail](mailto:team@example.org) [plain](http://example.org)',
            new CarveConfig(),
            new CarveConfig(urlPolicy: ['allow' => ['https']]),
            'engine default',
            "['allow' => ['https']]",
        );
        $profileSource = "= Heading\n\nText with /emphasis/.";
        $rows[] = [
            'option' => 'profile (per call)',
            'source' => $this->escaped($profileSource),
            'before' => $this->escaped(CarveRenderer::safe()->render($profileSource)),
            'after' => $this->escaped(CarveRenderer::safe()->render($profileSource, CarveProfile::Minimal)),
            'beforeLabel' => 'render($source)',
            'afterLabel' => 'render($source, CarveProfile::Minimal)',
        ];

        return $rows;
    }

    /**
     * @return list<array{name: string, config: string, html: string}>
     */
    private function named(): array
    {
        $source = "= Named renderers\n\n\"Smart\" quotes, a [link](http://example.org) and raw HTML:\n\n```=html\n<mark>raw</mark>\n```\n\n" . self::DISCLOSURE;
        $rows = [];
        $configs = [
            'comments' => "profile: Comment, smartTypography: false, headingIds: 'lower'",
            'static' => "mode: 'static', sections: false, headingIds: 'ascii-lower'",
            'trusted' => "rawHtml: true, urlPolicy: ['allow' => ['https', 'mailto']]",
        ];
        foreach ($configs as $name => $config) {
            $rows[] = ['name' => $name, 'config' => $config, 'html' => $this->escaped($this->carve->named($name)->render($source))];
        }

        return $rows;
    }

    /**
     * @return array{source: string, html: string}
     */
    private function wikiGenerator(): array
    {
        $source = 'See :wiki[Getting Started] and :wiki[Release Notes#upgrading|the upgrade notes].';
        $wikilinks = new ExtensionFactory()->create([
            'type' => 'wikilinks',
            'url_generator' => (new WikiLinkUrlTemplate('https://docs.example.org/{page}.html'))(...),
        ]);

        return [
            'source' => $this->escaped($source),
            'html' => $this->escaped(CarveRenderer::safe(extensions: [$wikilinks])->render($source)),
        ];
    }

    private function escaped(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
