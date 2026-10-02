<?php

declare(strict_types=1);

final class EvaluationService
{
    public static function forUser(int $userId, int $limit = 40): array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT ev.*, t.name AS test_name, e.title AS entry_title, e.type AS entry_type,
                    v.version AS version_number
             FROM evaluations ev
             INNER JOIN tests t ON t.id = ev.test_id
             INNER JOIN entries e ON e.id = t.entry_id
             LEFT JOIN entry_versions v ON v.id = ev.entry_version_id
             WHERE ev.user_id = :uid
             ORDER BY ev.created_at DESC
             LIMIT ' . (int) $limit
        );
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll();
    }

    public static function forTest(int $testId, int $userId): array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT ev.*, v.version AS version_number
             FROM evaluations ev
             LEFT JOIN entry_versions v ON v.id = ev.entry_version_id
             WHERE ev.test_id = :tid AND ev.user_id = :uid
             ORDER BY ev.created_at DESC'
        );
        $stmt->execute(['tid' => $testId, 'uid' => $userId]);
        return $stmt->fetchAll();
    }

    public static function find(int $id, int $userId): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT ev.*, t.name AS test_name, t.entry_id, e.title AS entry_title, e.type AS entry_type
             FROM evaluations ev
             INNER JOIN tests t ON t.id = ev.test_id
             INNER JOIN entries e ON e.id = t.entry_id
             WHERE ev.id = :id AND ev.user_id = :uid LIMIT 1'
        );
        $stmt->execute(['id' => $id, 'uid' => $userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(
        int $userId,
        int $testId,
        string $verdict,
        ?float $score,
        ?string $notes,
        ?string $modelLabel,
        ?string $outputSample,
        ?int $entryVersionId
    ): int {
        $test = TestService::find($testId, $userId);
        if (!$test) {
            throw new InvalidArgumentException('Prueba no encontrada.');
        }
        if (!isset(evaluation_verdicts()[$verdict])) {
            throw new InvalidArgumentException('Veredicto inválido.');
        }
        if ($score !== null && ($score < 0 || $score > 100)) {
            throw new InvalidArgumentException('El score debe estar entre 0 y 100.');
        }

        if ($entryVersionId === null) {
            $current = EntryService::currentVersion((int) $test['entry_id']);
            $entryVersionId = $current ? (int) $current['id'] : null;
        } else {
            $ver = EntryService::versionById($entryVersionId, $userId);
            if (!$ver || (int) $ver['entry_id'] !== (int) $test['entry_id']) {
                throw new InvalidArgumentException('Versión inválida para esta prueba.');
            }
        }

        $stmt = Database::pdo()->prepare(
            'INSERT INTO evaluations
             (user_id, test_id, entry_version_id, score, verdict, notes, model_label, output_sample)
             VALUES (:uid, :tid, :vid, :score, :verdict, :notes, :model, :output)'
        );
        $stmt->execute([
            'uid' => $userId,
            'tid' => $testId,
            'vid' => $entryVersionId,
            'score' => $score,
            'verdict' => $verdict,
            'notes' => null_if_blank($notes),
            'model' => null_if_blank($modelLabel),
            'output' => null_if_blank($outputSample),
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public static function delete(int $id, int $userId): void
    {
        $stmt = Database::pdo()->prepare('DELETE FROM evaluations WHERE id = :id AND user_id = :uid');
        $stmt->execute(['id' => $id, 'uid' => $userId]);
    }
}
