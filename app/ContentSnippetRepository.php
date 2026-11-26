<?php

declare(strict_types=1);

namespace App;

use PDO;
use PDOStatement;
use RuntimeException;

final class ContentSnippetRepository
{
    /** @var array<string, array{string, int}> */
    private const array SNIPPETS = [
        'handbook/overview' => ['handbook/overview.crv', 3],
        'handbook/details' => ['handbook/details.crv', 7],
        'handbook/targets.csv' => ['handbook/targets.csv', 2],
    ];

    private PDO $database;

    public function __construct()
    {
        $this->database = new PDO('sqlite::memory:');
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->database->exec('CREATE TABLE content_snippets (slug TEXT PRIMARY KEY, body TEXT NOT NULL, revision INTEGER NOT NULL)');

        $insert = $this->prepare('INSERT INTO content_snippets (slug, body, revision) VALUES (:slug, :body, :revision)');
        foreach (self::SNIPPETS as $slug => [$file, $revision]) {
            $insert->execute(['slug' => $slug, 'body' => Content::read('snippets/' . $file), 'revision' => $revision]);
        }
    }

    /** @return array{body: string, revision: int}|null */
    public function find(string $slug): ?array
    {
        $query = $this->prepare('SELECT body, revision FROM content_snippets WHERE slug = :slug');
        $query->execute(['slug' => $slug]);
        $row = $query->fetch(PDO::FETCH_ASSOC);

        if (! is_array($row)) {
            return null;
        }

        return ['body' => (string) $row['body'], 'revision' => (int) $row['revision']];
    }

    public function revisionKey(): string
    {
        $query = $this->database->query('SELECT slug, body, revision FROM content_snippets ORDER BY slug');
        if ($query === false) {
            throw new RuntimeException('Could not read content snippet revisions.');
        }

        $rows = $query->fetchAll(PDO::FETCH_ASSOC);

        return hash('sha256', serialize($rows));
    }

    private function prepare(string $sql): PDOStatement
    {
        $statement = $this->database->prepare($sql);
        if ($statement === false) {
            throw new RuntimeException('Could not prepare the content snippet query.');
        }

        return $statement;
    }
}
