<?php

declare(strict_types=1);

final class LibraryService
{
    public static function forUser(int $userId): array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT l.*,
                    (SELECT COUNT(*) FROM entries e WHERE e.library_id = l.id) AS entry_count
             FROM libraries l
             WHERE l.user_id = :uid
             ORDER BY l.name ASC'
        );
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll();
    }

    public static function find(int $id, int $userId): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT * FROM libraries WHERE id = :id AND user_id = :uid LIMIT 1'
        );
        $stmt->execute(['id' => $id, 'uid' => $userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(int $userId, string $name, ?string $description, string $color): int
    {
        $name = trim($name);
        if ($name === '') {
            throw new InvalidArgumentException('El nombre de la biblioteca es obligatorio.');
        }
        if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
            $color = '#0E9AA7';
        }
        $stmt = Database::pdo()->prepare(
            'INSERT INTO libraries (user_id, name, description, color)
             VALUES (:uid, :name, :description, :color)'
        );
        $stmt->execute([
            'uid' => $userId,
            'name' => $name,
            'description' => null_if_blank($description),
            'color' => strtoupper($color),
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public static function update(int $id, int $userId, string $name, ?string $description, string $color): void
    {
        if (!self::find($id, $userId)) {
            throw new InvalidArgumentException('Biblioteca no encontrada.');
        }
        $name = trim($name);
        if ($name === '') {
            throw new InvalidArgumentException('El nombre de la biblioteca es obligatorio.');
        }
        if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
            $color = '#0E9AA7';
        }
        $stmt = Database::pdo()->prepare(
            'UPDATE libraries SET name = :name, description = :description, color = :color
             WHERE id = :id AND user_id = :uid'
        );
        $stmt->execute([
            'name' => $name,
            'description' => null_if_blank($description),
            'color' => strtoupper($color),
            'id' => $id,
            'uid' => $userId,
        ]);
    }

    public static function delete(int $id, int $userId): void
    {
        $stmt = Database::pdo()->prepare('DELETE FROM libraries WHERE id = :id AND user_id = :uid');
        $stmt->execute(['id' => $id, 'uid' => $userId]);
    }
}
