<?php

declare(strict_types=1);

final class StatsService
{
    public static function forUser(int $userId): array
    {
        $pdo = Database::pdo();

        $count = static function (string $sql, array $params) use ($pdo): int {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            return (int) $stmt->fetchColumn();
        };

        $byType = [];
        foreach (array_keys(entry_types()) as $type) {
            $byType[$type] = $count(
                'SELECT COUNT(*) FROM entries WHERE user_id = :uid AND type = :type',
                ['uid' => $userId, 'type' => $type]
            );
        }

        $recent = $pdo->prepare(
            'SELECT id, type, title, status, current_version, updated_at
             FROM entries WHERE user_id = :uid
             ORDER BY updated_at DESC LIMIT 8'
        );
        $recent->execute(['uid' => $userId]);

        $recentEvals = $pdo->prepare(
            'SELECT ev.id, ev.verdict, ev.score, ev.created_at, t.name AS test_name, e.title AS entry_title
             FROM evaluations ev
             INNER JOIN tests t ON t.id = ev.test_id
             INNER JOIN entries e ON e.id = t.entry_id
             WHERE ev.user_id = :uid
             ORDER BY ev.created_at DESC LIMIT 6'
        );
        $recentEvals->execute(['uid' => $userId]);

        return [
            'libraries' => $count('SELECT COUNT(*) FROM libraries WHERE user_id = :uid', ['uid' => $userId]),
            'prompts' => $byType['prompt'] ?? 0,
            'instructions' => $byType['instruction'] ?? 0,
            'templates' => $byType['template'] ?? 0,
            'tests' => $count('SELECT COUNT(*) FROM tests WHERE user_id = :uid', ['uid' => $userId]),
            'evaluations' => $count('SELECT COUNT(*) FROM evaluations WHERE user_id = :uid', ['uid' => $userId]),
            'pass_rate' => self::passRate($userId),
            'recent_entries' => $recent->fetchAll(),
            'recent_evaluations' => $recentEvals->fetchAll(),
            'by_type' => $byType,
        ];
    }

    private static function passRate(int $userId): ?float
    {
        $stmt = Database::pdo()->prepare(
            'SELECT
                SUM(CASE WHEN verdict = "pass" THEN 1 ELSE 0 END) AS passes,
                COUNT(*) AS total
             FROM evaluations WHERE user_id = :uid'
        );
        $stmt->execute(['uid' => $userId]);
        $row = $stmt->fetch();
        if (!$row || (int) $row['total'] === 0) {
            return null;
        }
        return round(((int) $row['passes'] / (int) $row['total']) * 100, 1);
    }
}
