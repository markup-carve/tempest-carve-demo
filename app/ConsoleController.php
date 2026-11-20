<?php

declare(strict_types=1);

namespace App;

use Tempest\DateTime\Duration;
use Tempest\Process\PendingProcess;
use Tempest\Process\ProcessExecutor;
use Tempest\Router\Get;
use Tempest\Router\StaticPage;
use Tempest\View\View;

use function Tempest\View\view;

final readonly class ConsoleController
{
    /**
     * Each command runs as a real `php tempest` process, so the page shows the commands' own output.
     */
    private const array COMMANDS = [
        ['carve:render', 'content/formats.crv', '--format=markdown'],
        ['carve:render', 'content/formats.crv', '--format=text', '--renderer=excerpt'],
        ['carve:lint', 'content/samples/lint.crv'],
        ['carve:lint', 'content/samples/book.crv', '--include-root=content/samples'],
        ['carve:fmt', 'content/samples/unformatted.crv', '--align-tables'],
        ['carve:fmt', 'content/showcase.crv', '--check'],
        ['carve:convert', 'content/samples/import.html'],
        ['carve:convert', 'content/samples/import.md', '--from=markdown'],
    ];

    public function __construct(
        private ProcessExecutor $processes,
    ) {}

    #[Get(uri: '/console')]
    #[StaticPage]
    public function __invoke(): View
    {
        return view('console.view.php', runs: array_map($this->run(...), self::COMMANDS));
    }

    /**
     * @param list<string> $arguments
     * @return array{command: string, exit: int, output: string}
     */
    private function run(array $arguments): array
    {
        $root = dirname(__DIR__);
        $result = $this->processes->run(new PendingProcess(
            // A string command: Tempest's testing executor records string commands only.
            command: implode(' ', array_map(escapeshellarg(...), [PHP_BINARY, $root . '/tempest', ...$arguments])),
            timeout: Duration::seconds(60),
            path: $root,
            environment: ['NO_COLOR' => '1'],
        ));
        $output = rtrim((string) preg_replace('/\e\[[0-9;]*m/', '', $result->output . $result->errorOutput));

        return [
            'command' => 'php tempest ' . implode(' ', $arguments),
            'exit' => $result->exitCode,
            'output' => htmlspecialchars($output === '' ? '(no output)' : $output, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
        ];
    }
}
