<?php

declare(strict_types=1);

namespace App;

use MarkupCarve\Tempest\CarveRenderer;
use Tempest\Router\Get;
use Tempest\Router\StaticPage;
use Tempest\View\View;

use function Tempest\View\view;

final readonly class CodeLinesController
{
    public function __construct(
        private CarveRenderer $carve,
    ) {}

    #[Get(uri: '/code-lines')]
    #[StaticPage]
    public function __invoke(): View
    {
        $source = Content::read('code-lines.crv');

        return view(
            'code-lines.view.php',
            source: htmlspecialchars($source, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
            html: $this->carve->render($source),
        );
    }
}
