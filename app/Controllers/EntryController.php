<?php

declare(strict_types=1);

final class EntryController
{
    public static function indexPrompts(): void
    {
        self::index('prompt');
    }

    public static function indexInstructions(): void
    {
        self::index('instruction');
    }

    public static function indexTemplates(): void
    {
        self::index('template');
    }

    private static function index(string $type): void
    {
        Auth::requireLogin();
        $status = null_if_blank((string) input('status', ''));
        $libraryId = int_or_null(input('library_id'));
        $q = null_if_blank((string) input('q', ''));
        view('entries/index', [
            'title' => entry_types()[$type] . 's',
            'type' => $type,
            'entries' => EntryService::list(Auth::id(), $type, $status, $libraryId, $q),
            'libraries' => LibraryService::forUser(Auth::id()),
            'filters' => [
                'status' => $status,
                'library_id' => $libraryId,
                'q' => $q,
            ],
        ]);
    }

    public static function create(string $type): void
    {
        Auth::requireLogin();
        EntryService::assertType($type);
        view('entries/form', [
            'title' => 'Nuevo ' . mb_strtolower(entry_type_label($type)),
            'type' => $type,
            'entry' => null,
            'version' => null,
            'libraries' => LibraryService::forUser(Auth::id()),
            'mode' => 'create',
        ]);
    }

    public static function store(string $type): void
    {
        Auth::requireLogin();
        require_csrf();
        try {
            $id = EntryService::create(
                Auth::id(),
                $type,
                (string) input('title', ''),
                (string) input('body', ''),
                (string) input('description', ''),
                int_or_null(input('library_id')),
                (string) input('status', 'draft'),
                (string) input('tags', ''),
                (string) input('model_hint', ''),
                (string) input('changelog', '')
            );
            flash('success', entry_type_label($type) . ' creado.');
            redirect(entry_type_path($type) . '/' . $id);
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            redirect(entry_type_path($type) . '/nuevo');
        }
    }

    public static function show(string $type, string $id): void
    {
        Auth::requireLogin();
        $entry = EntryService::find((int) $id, Auth::id());
        if (!$entry || $entry['type'] !== $type) {
            flash('error', 'No encontrado.');
            redirect(entry_type_path($type));
        }
        view('entries/show', [
            'title' => $entry['title'],
            'type' => $type,
            'entry' => $entry,
            'version' => EntryService::currentVersion((int) $id),
            'versions' => EntryService::versions((int) $id),
            'tests' => TestService::forEntry((int) $id, Auth::id()),
        ]);
    }

    public static function edit(string $type, string $id): void
    {
        Auth::requireLogin();
        $entry = EntryService::find((int) $id, Auth::id());
        if (!$entry || $entry['type'] !== $type) {
            flash('error', 'No encontrado.');
            redirect(entry_type_path($type));
        }
        view('entries/form', [
            'title' => 'Editar metadatos',
            'type' => $type,
            'entry' => $entry,
            'version' => EntryService::currentVersion((int) $id),
            'libraries' => LibraryService::forUser(Auth::id()),
            'mode' => 'meta',
        ]);
    }

    public static function update(string $type, string $id): void
    {
        Auth::requireLogin();
        require_csrf();
        try {
            EntryService::updateMeta(
                (int) $id,
                Auth::id(),
                (string) input('title', ''),
                (string) input('description', ''),
                int_or_null(input('library_id')),
                (string) input('status', 'draft'),
                (string) input('tags', ''),
                (string) input('model_hint', '')
            );
            flash('success', 'Metadatos actualizados.');
            redirect(entry_type_path($type) . '/' . $id);
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            redirect(entry_type_path($type) . '/' . $id . '/editar');
        }
    }

    public static function newVersionForm(string $type, string $id): void
    {
        Auth::requireLogin();
        $entry = EntryService::find((int) $id, Auth::id());
        if (!$entry || $entry['type'] !== $type) {
            flash('error', 'No encontrado.');
            redirect(entry_type_path($type));
        }
        view('entries/form', [
            'title' => 'Nueva versión',
            'type' => $type,
            'entry' => $entry,
            'version' => EntryService::currentVersion((int) $id),
            'libraries' => LibraryService::forUser(Auth::id()),
            'mode' => 'version',
        ]);
    }

    public static function storeVersion(string $type, string $id): void
    {
        Auth::requireLogin();
        require_csrf();
        try {
            $v = EntryService::saveNewVersion(
                (int) $id,
                Auth::id(),
                (string) input('body', ''),
                (string) input('changelog', '')
            );
            flash('success', 'Versión v' . $v . ' guardada.');
            redirect(entry_type_path($type) . '/' . $id);
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            redirect(entry_type_path($type) . '/' . $id . '/nueva-version');
        }
    }

    public static function destroy(string $type, string $id): void
    {
        Auth::requireLogin();
        require_csrf();
        $entry = EntryService::find((int) $id, Auth::id());
        if ($entry && $entry['type'] === $type) {
            EntryService::delete((int) $id, Auth::id());
            flash('success', 'Eliminado.');
        }
        redirect(entry_type_path($type));
    }

    public static function export(string $type, string $id): void
    {
        Auth::requireLogin();
        $format = strtolower((string) input('format', 'json'));
        try {
            $payload = EntryService::exportPayload((int) $id, Auth::id());
            if (($payload['entry']['type'] ?? '') !== $type) {
                throw new InvalidArgumentException('Tipo incorrecto.');
            }
            $slug = preg_replace('/[^a-z0-9_-]+/i', '-', (string) $payload['entry']['title']) ?: 'entry';
            $slug = trim($slug, '-');
            if ($format === 'md' || $format === 'markdown') {
                $body = EntryService::toMarkdown($payload);
                header('Content-Type: text/markdown; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . $slug . '.md"');
                echo $body;
                exit;
            }
            header('Content-Type: application/json; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $slug . '.json"');
            echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            exit;
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            redirect(entry_type_path($type) . '/' . $id);
        }
    }
}
