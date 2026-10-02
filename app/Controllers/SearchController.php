<?php

declare(strict_types=1);

final class SearchController
{
    public static function index(): void
    {
        Auth::requireLogin();
        $q = trim((string) input('q', ''));
        view('search/index', [
            'title' => 'Buscar',
            'q' => $q,
            'results' => $q !== '' ? SearchService::query(Auth::id(), $q) : [
                'entries' => [],
                'tests' => [],
                'libraries' => [],
            ],
        ]);
    }
}
