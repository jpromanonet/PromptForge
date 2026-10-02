<?php

declare(strict_types=1);

final class EntryService
{
    public static function list(int $userId, string $type, ?string $status = null, ?int $libraryId = null, ?string $q = null): array
    {
        $sql = 'SELECT e.*, l.name AS library_name, l.color AS library_color,
                       (SELECT COUNT(*) FROM tests t WHERE t.entry_id = e.id) AS test_count
                FROM entries e
                LEFT JOIN libraries l ON l.id = e.library_id
                WHERE e.user_id = :uid AND e.type = :type';
        $params = ['uid' => $userId, 'type' => $type];

        if ($status !== null && $status !== '') {
            $sql .= ' AND e.status = :status';
            $params['status'] = $status;
        }
        if ($libraryId !== null && $libraryId > 0) {
            $sql .= ' AND e.library_id = :lid';
            $params['lid'] = $libraryId;
        }
        if ($q !== null && trim($q) !== '') {
            $sql .= ' AND (e.title LIKE :q OR e.description LIKE :q OR e.tags LIKE :q)';
            $params['q'] = '%' . trim($q) . '%';
        }

        $sql .= ' ORDER BY e.updated_at DESC';
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function find(int $id, int $userId): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT e.*, l.name AS library_name, l.color AS library_color
             FROM entries e
             LEFT JOIN libraries l ON l.id = e.library_id
             WHERE e.id = :id AND e.user_id = :uid LIMIT 1'
        );
        $stmt->execute(['id' => $id, 'uid' => $userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function currentVersion(int $entryId): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT v.* FROM entry_versions v
             INNER JOIN entries e ON e.id = v.entry_id
             WHERE v.entry_id = :id AND v.version = e.current_version
             LIMIT 1'
        );
        $stmt->execute(['id' => $entryId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function versions(int $entryId): array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT * FROM entry_versions WHERE entry_id = :id ORDER BY version DESC'
        );
        $stmt->execute(['id' => $entryId]);
        return $stmt->fetchAll();
    }

    public static function versionById(int $versionId, int $userId): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT v.*, e.user_id, e.type, e.title
             FROM entry_versions v
             INNER JOIN entries e ON e.id = v.entry_id
             WHERE v.id = :vid AND e.user_id = :uid LIMIT 1'
        );
        $stmt->execute(['vid' => $versionId, 'uid' => $userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(
        int $userId,
        string $type,
        string $title,
        string $body,
        ?string $description,
        ?int $libraryId,
        string $status,
        ?string $tags,
        ?string $modelHint,
        ?string $changelog
    ): int {
        self::assertType($type);
        $title = trim($title);
        $body = trim($body);
        if ($title === '') {
            throw new InvalidArgumentException('El título es obligatorio.');
        }
        if ($body === '') {
            throw new InvalidArgumentException('El cuerpo del prompt es obligatorio.');
        }
        if (!isset(entry_statuses()[$status])) {
            $status = 'draft';
        }
        if ($libraryId !== null && $libraryId > 0) {
            if (!LibraryService::find($libraryId, $userId)) {
                throw new InvalidArgumentException('Biblioteca inválida.');
            }
        } else {
            $libraryId = null;
        }

        $vars = extract_variables($body);
        $pdo = Database::pdo();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO entries (user_id, library_id, type, title, description, status, tags, model_hint, current_version)
                 VALUES (:uid, :lid, :type, :title, :description, :status, :tags, :model_hint, 1)'
            );
            $stmt->execute([
                'uid' => $userId,
                'lid' => $libraryId,
                'type' => $type,
                'title' => $title,
                'description' => null_if_blank($description),
                'status' => $status,
                'tags' => null_if_blank($tags),
                'model_hint' => null_if_blank($modelHint),
            ]);
            $entryId = (int) $pdo->lastInsertId();

            $v = $pdo->prepare(
                'INSERT INTO entry_versions (entry_id, version, body, variables_json, changelog, created_by)
                 VALUES (:eid, 1, :body, :vars, :changelog, :uid)'
            );
            $v->execute([
                'eid' => $entryId,
                'body' => $body,
                'vars' => $vars ? json_encode($vars, JSON_UNESCAPED_UNICODE) : null,
                'changelog' => null_if_blank($changelog) ?? 'Versión inicial',
                'uid' => $userId,
            ]);
            $pdo->commit();
            return $entryId;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function updateMeta(
        int $id,
        int $userId,
        string $title,
        ?string $description,
        ?int $libraryId,
        string $status,
        ?string $tags,
        ?string $modelHint
    ): void {
        $entry = self::find($id, $userId);
        if (!$entry) {
            throw new InvalidArgumentException('Entrada no encontrada.');
        }
        $title = trim($title);
        if ($title === '') {
            throw new InvalidArgumentException('El título es obligatorio.');
        }
        if (!isset(entry_statuses()[$status])) {
            $status = $entry['status'];
        }
        if ($libraryId !== null && $libraryId > 0) {
            if (!LibraryService::find($libraryId, $userId)) {
                throw new InvalidArgumentException('Biblioteca inválida.');
            }
        } else {
            $libraryId = null;
        }

        $stmt = Database::pdo()->prepare(
            'UPDATE entries
             SET title = :title, description = :description, library_id = :lid,
                 status = :status, tags = :tags, model_hint = :model_hint
             WHERE id = :id AND user_id = :uid'
        );
        $stmt->execute([
            'title' => $title,
            'description' => null_if_blank($description),
            'lid' => $libraryId,
            'status' => $status,
            'tags' => null_if_blank($tags),
            'model_hint' => null_if_blank($modelHint),
            'id' => $id,
            'uid' => $userId,
        ]);
    }

    public static function saveNewVersion(int $id, int $userId, string $body, ?string $changelog): int
    {
        $entry = self::find($id, $userId);
        if (!$entry) {
            throw new InvalidArgumentException('Entrada no encontrada.');
        }
        $body = trim($body);
        if ($body === '') {
            throw new InvalidArgumentException('El cuerpo no puede estar vacío.');
        }

        $vars = extract_variables($body);
        $next = (int) $entry['current_version'] + 1;
        $pdo = Database::pdo();
        $pdo->beginTransaction();
        try {
            $v = $pdo->prepare(
                'INSERT INTO entry_versions (entry_id, version, body, variables_json, changelog, created_by)
                 VALUES (:eid, :version, :body, :vars, :changelog, :uid)'
            );
            $v->execute([
                'eid' => $id,
                'version' => $next,
                'body' => $body,
                'vars' => $vars ? json_encode($vars, JSON_UNESCAPED_UNICODE) : null,
                'changelog' => null_if_blank($changelog) ?? ('v' . $next),
                'uid' => $userId,
            ]);
            $upd = $pdo->prepare('UPDATE entries SET current_version = :v WHERE id = :id');
            $upd->execute(['v' => $next, 'id' => $id]);
            $pdo->commit();
            return $next;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function delete(int $id, int $userId): void
    {
        $stmt = Database::pdo()->prepare('DELETE FROM entries WHERE id = :id AND user_id = :uid');
        $stmt->execute(['id' => $id, 'uid' => $userId]);
    }

    public static function assertType(string $type): void
    {
        if (!isset(entry_types()[$type])) {
            throw new InvalidArgumentException('Tipo de entrada inválido.');
        }
    }

    public static function exportPayload(int $id, int $userId): array
    {
        $entry = self::find($id, $userId);
        if (!$entry) {
            throw new InvalidArgumentException('Entrada no encontrada.');
        }
        $versions = self::versions($id);
        $tests = TestService::forEntry($id, $userId);
        $evals = [];
        foreach ($tests as $t) {
            $evals[(int) $t['id']] = EvaluationService::forTest((int) $t['id'], $userId);
        }

        return [
            'exported_at' => date('c'),
            'app' => 'PromptForge',
            'entry' => [
                'id' => (int) $entry['id'],
                'type' => $entry['type'],
                'title' => $entry['title'],
                'description' => $entry['description'],
                'status' => $entry['status'],
                'tags' => parse_tags($entry['tags'] ?? null),
                'model_hint' => $entry['model_hint'],
                'library' => $entry['library_name'],
                'current_version' => (int) $entry['current_version'],
            ],
            'versions' => array_map(static function (array $v): array {
                return [
                    'version' => (int) $v['version'],
                    'body' => $v['body'],
                    'variables' => $v['variables_json'] ? json_decode((string) $v['variables_json'], true) : [],
                    'changelog' => $v['changelog'],
                    'created_at' => $v['created_at'],
                ];
            }, $versions),
            'tests' => array_map(static function (array $t) use ($evals): array {
                return [
                    'name' => $t['name'],
                    'input' => $t['input_text'],
                    'expected' => $t['expected_output'],
                    'criteria' => $t['criteria'],
                    'evaluations' => $evals[(int) $t['id']] ?? [],
                ];
            }, $tests),
        ];
    }

    public static function toMarkdown(array $payload): string
    {
        $e = $payload['entry'];
        $lines = [];
        $lines[] = '# ' . $e['title'];
        $lines[] = '';
        $lines[] = '- **Tipo:** ' . entry_type_label((string) $e['type']);
        $lines[] = '- **Estado:** ' . entry_status_label((string) $e['status']);
        $lines[] = '- **Versión actual:** v' . $e['current_version'];
        if (!empty($e['library'])) {
            $lines[] = '- **Biblioteca:** ' . $e['library'];
        }
        if (!empty($e['tags'])) {
            $lines[] = '- **Tags:** ' . implode(', ', $e['tags']);
        }
        if (!empty($e['model_hint'])) {
            $lines[] = '- **Modelo sugerido:** ' . $e['model_hint'];
        }
        if (!empty($e['description'])) {
            $lines[] = '';
            $lines[] = '## Descripción';
            $lines[] = '';
            $lines[] = $e['description'];
        }
        $lines[] = '';
        $lines[] = '## Versiones';
        foreach ($payload['versions'] as $v) {
            $lines[] = '';
            $lines[] = '### v' . $v['version'];
            if (!empty($v['changelog'])) {
                $lines[] = '_' . $v['changelog'] . '_';
            }
            $lines[] = '';
            $lines[] = '```';
            $lines[] = $v['body'];
            $lines[] = '```';
        }
        if (!empty($payload['tests'])) {
            $lines[] = '';
            $lines[] = '## Pruebas';
            foreach ($payload['tests'] as $t) {
                $lines[] = '';
                $lines[] = '### ' . $t['name'];
                if ($t['input']) {
                    $lines[] = '**Input:**';
                    $lines[] = '';
                    $lines[] = $t['input'];
                }
                if ($t['expected']) {
                    $lines[] = '';
                    $lines[] = '**Esperado:**';
                    $lines[] = '';
                    $lines[] = $t['expected'];
                }
                if ($t['criteria']) {
                    $lines[] = '';
                    $lines[] = '**Criterios:** ' . $t['criteria'];
                }
            }
        }
        $lines[] = '';
        $lines[] = '---';
        $lines[] = '_Exportado desde PromptForge · ' . ($payload['exported_at'] ?? '') . '_';
        return implode("\n", $lines) . "\n";
    }
}
