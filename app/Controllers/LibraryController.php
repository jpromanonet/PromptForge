<?php

declare(strict_types=1);

final class LibraryController
{
    public static function index(): void
    {
        Auth::requireLogin();
        view('libraries/index', [
            'title' => 'Bibliotecas',
            'libraries' => LibraryService::forUser(Auth::id()),
        ]);
    }

    public static function create(): void
    {
        Auth::requireLogin();
        view('libraries/form', [
            'title' => 'Nueva biblioteca',
            'library' => null,
        ]);
    }

    public static function store(): void
    {
        Auth::requireLogin();
        require_csrf();
        try {
            $id = LibraryService::create(
                Auth::id(),
                (string) input('name', ''),
                (string) input('description', ''),
                (string) input('color', '#0E9AA7')
            );
            flash('success', 'Biblioteca creada.');
            redirect('/bibliotecas/' . $id);
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            redirect('/bibliotecas/nueva');
        }
    }

    public static function show(string $id): void
    {
        Auth::requireLogin();
        $library = LibraryService::find((int) $id, Auth::id());
        if (!$library) {
            flash('error', 'Biblioteca no encontrada.');
            redirect('/bibliotecas');
        }
        $entries = [];
        foreach (array_keys(entry_types()) as $type) {
            $entries[$type] = EntryService::list(Auth::id(), $type, null, (int) $id);
        }
        view('libraries/show', [
            'title' => $library['name'],
            'library' => $library,
            'entries' => $entries,
        ]);
    }

    public static function edit(string $id): void
    {
        Auth::requireLogin();
        $library = LibraryService::find((int) $id, Auth::id());
        if (!$library) {
            flash('error', 'Biblioteca no encontrada.');
            redirect('/bibliotecas');
        }
        view('libraries/form', [
            'title' => 'Editar biblioteca',
            'library' => $library,
        ]);
    }

    public static function update(string $id): void
    {
        Auth::requireLogin();
        require_csrf();
        try {
            LibraryService::update(
                (int) $id,
                Auth::id(),
                (string) input('name', ''),
                (string) input('description', ''),
                (string) input('color', '#0E9AA7')
            );
            flash('success', 'Biblioteca actualizada.');
            redirect('/bibliotecas/' . $id);
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            redirect('/bibliotecas/' . $id . '/editar');
        }
    }

    public static function destroy(string $id): void
    {
        Auth::requireLogin();
        require_csrf();
        LibraryService::delete((int) $id, Auth::id());
        flash('success', 'Biblioteca eliminada.');
        redirect('/bibliotecas');
    }
}
