<?php

declare(strict_types=1);

namespace App;

use MarkupCarve\Carve\CarveException;
use MarkupCarve\Tempest\CarveRenderer;
use MarkupCarve\Tempest\IncludeOptions;
use MarkupCarve\Tempest\LintFinding;
use Tempest\Router\Get;
use Tempest\Router\StaticPage;
use Tempest\Validation\Validator;
use Tempest\View\View;

use function Tempest\Mapper\map;
use function Tempest\View\view;

final readonly class AuthoringController
{
    public function __construct(
        private CarveRenderer $carve,
        private Validator $validator,
    ) {}

    #[Get(uri: '/authoring')]
    #[StaticPage]
    public function __invoke(): View
    {
        $lintSource = Content::read('samples/lint.crv');
        $unformatted = Content::read('samples/unformatted.crv');
        $html = Content::read('samples/import.html');
        $markdown = Content::read('samples/import.md');

        return view(
            'authoring.view.php',
            lintSource: $this->escaped($lintSource),
            lint: $this->findings($this->carve->lint($lintSource)),
            lintIncluded: $this->findings($this->carve->lintIncluded($lintSource, 'db:samples/lint')),
            unformatted: $this->escaped($unformatted),
            formatted: $this->escaped($this->carve->format($unformatted)),
            aligned: $this->escaped($this->carve->format($unformatted, alignTables: true)),
            htmlSource: $this->escaped($html),
            fromHtml: $this->escaped($this->carve->fromHtml($html)),
            markdownSource: $this->escaped($markdown),
            fromMarkdown: $this->escaped($this->fromMarkdown($markdown)),
            validation: $this->validation(),
            post: $this->post(),
            strictLosses: $this->strictLosses(),
            limits: $this->includeLimits(),
        );
    }

    /**
     * @param list<LintFinding> $findings
     * @return list<array{rule: string, position: string, message: string}>
     */
    private function findings(array $findings): array
    {
        return array_map(
            static fn (LintFinding $finding): array => [
                'rule' => $finding->rule,
                'position' => sprintf('%sL%d:C%d', $finding->file === null ? '' : $finding->file . ' ', $finding->line, $finding->column),
                'message' => $finding->message,
            ],
            $findings,
        );
    }

    private function fromMarkdown(string $markdown): string
    {
        try {
            return $this->carve->fromMarkdown($markdown);
        } catch (CarveException $exception) {
            return 'Markdown import is unavailable: ' . $exception->getMessage();
        }
    }

    /**
     * @return list<array{input: string, valid: bool, message: string}>
     */
    private function validation(): array
    {
        $inputs = [
            'Nice /work/, thanks!',
            '= A heading in a comment',
            'Old habit: a _bare_ underline',
            str_repeat('Too long. ', 13),
        ];
        $rows = [];
        foreach ($inputs as $input) {
            $failures = $this->validator->validateValuesForClass(CommentForm::class, ['body' => $input]);
            $messages = array_map(fn ($failure): string => $this->validator->getErrorMessage($failure), $failures['body'] ?? []);
            $rows[] = ['input' => $input, 'valid' => $messages === [], 'message' => $messages === [] ? 'Valid' : implode(' ', $messages)];
        }

        return $rows;
    }

    /**
     * @return array{source: string, html: string, comments: string, stored: string}
     */
    private function post(): array
    {
        /** @var Post $post */
        $post = map(['title' => 'Release day', 'body' => "= Release day\n\nThe /new/ version is out."])->to(Post::class);

        return [
            'source' => $this->escaped($post->body->source),
            'html' => $this->escaped((string) $post->body),
            'comments' => $this->escaped($post->body->html('comments')),
            'stored' => $this->escaped(json_encode(map($post)->toArray(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)),
        ];
    }

    private function strictLosses(): string
    {
        try {
            CarveRenderer::safe()->renderWithReport(Content::read('loss.crv'), strictLosses: true);

            return 'Rendered without a loss.';
        } catch (CarveException $exception) {
            return $exception::class . ': ' . $exception->getMessage();
        }
    }

    /**
     * @return list<array{options: string, warnings: list<string>, suppressed: int|null}>
     */
    private function includeLimits(): array
    {
        $source = Content::read('includes.crv');
        $missing = "{{ /handbook/draft-one }}\n\n{{ /handbook/draft-two }}\n\n{{ /handbook/draft-three }}";
        $rows = [];
        $cases = [
            'depthLimit: 1' => [$source, new IncludeOptions(currentPath: 'db:handbook/root', depthLimit: 1)],
            'byteBudget: 120' => [$source, new IncludeOptions(currentPath: 'db:handbook/root', byteBudget: 120)],
            'warningLimit: 1, three missing rows' => [$missing, new IncludeOptions(currentPath: 'db:handbook/root', warningLimit: 1)],
        ];
        foreach ($cases as $label => [$input, $options]) {
            $result = $this->carve->renderIncluded($input, $options);
            $rows[] = [
                'options' => $label,
                'warnings' => array_map(static fn (array $warning): string => sprintf('%s: %s', (string) $warning['code'], (string) $warning['message']), $result->warnings),
                'suppressed' => $result->suppressedWarnings,
            ];
        }

        return $rows;
    }

    private function escaped(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
