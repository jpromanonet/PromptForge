<?php

declare(strict_types=1);

final class TestService
{
    public static function forUser(int $userId, ?int $entryId = null): array
    {
        $sql = 'SELECT t.*, e.title AS entry_title, e.type AS entry_type,
                       (SELECT COUNT(*) FROM evaluations ev WHERE ev.test_id = t.id) AS eval_count,
                       (SELECT ev2.verdict FROM evaluations ev2 WHERE ev2.test_id = t.id ORDER BY ev2.created_at DESC LIMIT 1) AS last_verdict
                FROM tests t
                INNER JOIN entries e ON e.id = t.entry_id
                WHERE t.user_id = :uid';
        $params = ['uid' => $userId];
        if ($entryId !== null) {
            $sql .= ' AND t.entry_id = :eid';
            $params['eid'] = $entryId;
        }
        $sql .= ' ORDER BY t.updated_at DESC';
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function forEntry(int $entryId, int $userId): array
    {
        return self::forUser($userId, $entryId);
    }

    public static function find(int $id, int $userId): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT t.*, e.title AS entry_title, e.type AS entry_type, e.current_version
             FROM tests t
             INNER JOIN entries e ON e.id = t.entry_id
             WHERE t.id = :id AND t.user_id = :uid LIMIT 1'
        );
        $stmt->execute(['id' => $id, 'uid' => $userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(
        int $userId,
        int $entryId,
        string $name,
        ?string $input,
        ?string $expected,
        ?string $criteria
    ): int {
        if (!EntryService::find($entryId, $userId)) {
            throw new InvalidArgumentException('Entrada no encontrada.');
        }
        $name = trim($name);
        if ($name === '') {
            throw new InvalidArgumentException('El nombre de la prueba es obligatorio.');
        }
        $stmt = Database::pdo()->prepare(
            'INSERT INTO tests (user_id, entry_id, name, input_text, expected_output, criteria)
             VALUES (:uid, :eid, :name, :input, :expected, :criteria)'
        );
        $stmt->execute([
            'uid' => $userId,
            'eid' => $entryId,
            'name' => $name,
            'input' => null_if_blank($input),
            'expected' => null_if_blank($expected),
            'criteria' => null_if_blank($criteria),
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public static function update(
        int $id,
        int $userId,
        string $name,
        ?string $input,
        ?string $expected,
        ?string $criteria,
        bool $isActive
    ): void {
        if (!self::find($id, $userId)) {
            throw new InvalidArgumentException('Prueba no encontrada.');
        }
        $name = trim($name);
        if ($name === '') {
            throw new InvalidArgumentException('El nombre de la prueba es obligatorio.');
        }
        $stmt = Database::pdo()->prepare(
            'UPDATE tests
             SET name = :name, input_text = :input, expected_output = :expected,
                 criteria = :criteria, is_active = :active
             WHERE id = :id AND user_id = :uid'
        );
        $stmt->execute([
            'name' => $name,
            'input' => null_if_blank($input),
            'expected' => null_if_blank($expected),
            'criteria' => null_if_blank($criteria),
            'active' => $isActive ? 1 : 0,
            'id' => $id,
            'uid' => $userId,
        ]);
    }

    public static function delete(int $id, int $userId): void
    {
        $stmt = Database::pdo()->prepare('DELETE FROM tests WHERE id = :id AND user_id = :uid');
        $stmt->execute(['id' => $id, 'uid' => $userId]);
    }
}
