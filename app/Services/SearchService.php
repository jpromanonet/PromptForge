<?php

declare(strict_types=1);

final class SearchService
{
    public static function query(int $userId, string $q): array
    {
        $q = trim($q);
        if ($q === '') {
            return ['entries' => [], 'tests' => [], 'libraries' => []];
        }
        $like = '%' . $q . '%';
        $pdo = Database::pdo();

        $entries = $pdo->prepare(
            'SELECT id, type, title, status, current_version, updated_at
             FROM entries
             WHERE user_id = :uid AND (title LIKE :q OR description LIKE :q OR tags LIKE :q OR model_hint LIKE :q)
             ORDER BY updated_at DESC LIMIT 30'
        );
        $entries->execute(['uid' => $userId, 'q' => $like]);

        $tests = $pdo->prepare(
            'SELECT t.id, t.name, e.title AS entry_title, e.type AS entry_type
             FROM tests t
             INNER JOIN entries e ON e.id = t.entry_id
             WHERE t.user_id = :uid AND (t.name LIKE :q OR t.input_text LIKE :q OR t.criteria LIKE :q)
             ORDER BY t.updated_at DESC LIMIT 20'
        );
        $tests->execute(['uid' => $userId, 'q' => $like]);

        $libs = $pdo->prepare(
            'SELECT id, name, description, color FROM libraries
             WHERE user_id = :uid AND (name LIKE :q OR description LIKE :q)
             ORDER BY name ASC LIMIT 15'
        );
        $libs->execute(['uid' => $userId, 'q' => $like]);

        // Also search version bodies
        $bodies = $pdo->prepare(
            'SELECT DISTINCT e.id, e.type, e.title, e.status, e.current_version, e.updated_at
             FROM entry_versions v
             INNER JOIN entries e ON e.id = v.entry_id
             WHERE e.user_id = :uid AND v.body LIKE :q
             ORDER BY e.updated_at DESC LIMIT 20'
        );
        $bodies->execute(['uid' => $userId, 'q' => $like]);

        $entryRows = $entries->fetchAll();
        $seen = [];
        foreach ($entryRows as $row) {
            $seen[(int) $row['id']] = true;
        }
        foreach ($bodies->fetchAll() as $row) {
            if (!isset($seen[(int) $row['id']])) {
                $entryRows[] = $row;
            }
        }

        return [
            'entries' => $entryRows,
            'tests' => $tests->fetchAll(),
            'libraries' => $libs->fetchAll(),
        ];
    }
}
