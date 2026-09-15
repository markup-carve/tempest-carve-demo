<?php

declare(strict_types=1);

namespace App;

use PDO;
use PDOStatement;
use RuntimeException;

final class ContentSnippetRepository
{
    private PDO $database;

    public function __construct()
    {
        $this->database = new PDO('sqlite::memory:');
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->database->exec('CREATE TABLE content_snippets (slug TEXT PRIMARY KEY, body TEXT NOT NULL, revision INTEGER NOT NULL)');

        $insert = $this->prepare('INSERT INTO content_snippets (slug, body, revision) VALUES (:slug, :body, :revision)');
        $insert->execute([
            'slug' => 'handbook/overview',
            'body' => "# Database chapter\n\nThis Carve source was loaded from an in-memory SQLite row.\n\n{{ details @shift:auto }}",
            'revision' => 3,
        ]);
        $insert->execute([
            'slug' => 'handbook/details',
            'body' => "# Nested database snippet\n\nThe resolver uses the including record as context, so relative includes work across rows.",
            'revision' => 7,
        ]);
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
