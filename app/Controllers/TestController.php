<?php

declare(strict_types=1);

final class TestController
{
    public static function index(): void
    {
        Auth::requireLogin();
        view('tests/index', [
            'title' => 'Pruebas',
            'tests' => TestService::forUser(Auth::id()),
        ]);
    }

    public static function create(): void
    {
        Auth::requireLogin();
        $entryId = int_or_null(input('entry_id'));
        view('tests/form', [
            'title' => 'Nueva prueba',
            'test' => null,
            'entryId' => $entryId,
            'entries' => self::entryOptions(),
        ]);
    }

    public static function store(): void
    {
        Auth::requireLogin();
        require_csrf();
        try {
            $id = TestService::create(
                Auth::id(),
                (int) input('entry_id', 0),
                (string) input('name', ''),
                (string) input('input_text', ''),
                (string) input('expected_output', ''),
                (string) input('criteria', '')
            );
            flash('success', 'Prueba creada.');
            redirect('/pruebas/' . $id);
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            redirect('/pruebas/nueva');
        }
    }

    public static function show(string $id): void
    {
        Auth::requireLogin();
        $test = TestService::find((int) $id, Auth::id());
        if (!$test) {
            flash('error', 'Prueba no encontrada.');
            redirect('/pruebas');
        }
        $entry = EntryService::find((int) $test['entry_id'], Auth::id());
        view('tests/show', [
            'title' => $test['name'],
            'test' => $test,
            'entry' => $entry,
            'evaluations' => EvaluationService::forTest((int) $id, Auth::id()),
            'versions' => EntryService::versions((int) $test['entry_id']),
        ]);
    }

    public static function edit(string $id): void
    {
        Auth::requireLogin();
        $test = TestService::find((int) $id, Auth::id());
        if (!$test) {
            flash('error', 'Prueba no encontrada.');
            redirect('/pruebas');
        }
        view('tests/form', [
            'title' => 'Editar prueba',
            'test' => $test,
            'entryId' => (int) $test['entry_id'],
            'entries' => self::entryOptions(),
        ]);
    }

    public static function update(string $id): void
    {
        Auth::requireLogin();
        require_csrf();
        try {
            TestService::update(
                (int) $id,
                Auth::id(),
                (string) input('name', ''),
                (string) input('input_text', ''),
                (string) input('expected_output', ''),
                (string) input('criteria', ''),
                (string) input('is_active', '1') === '1'
            );
            flash('success', 'Prueba actualizada.');
            redirect('/pruebas/' . $id);
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            redirect('/pruebas/' . $id . '/editar');
        }
    }

    public static function destroy(string $id): void
    {
        Auth::requireLogin();
        require_csrf();
        TestService::delete((int) $id, Auth::id());
        flash('success', 'Prueba eliminada.');
        redirect('/pruebas');
    }

    private static function entryOptions(): array
    {
        $out = [];
        foreach (array_keys(entry_types()) as $type) {
            foreach (EntryService::list(Auth::id(), $type) as $row) {
                $out[] = $row;
            }
        }
        usort($out, static fn ($a, $b) => strcmp((string) $a['title'], (string) $b['title']));
        return $out;
    }
}
