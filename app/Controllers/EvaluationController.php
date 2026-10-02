<?php

declare(strict_types=1);

final class EvaluationController
{
    public static function index(): void
    {
        Auth::requireLogin();
        view('evaluations/index', [
            'title' => 'Evaluaciones',
            'evaluations' => EvaluationService::forUser(Auth::id()),
        ]);
    }

    public static function store(): void
    {
        Auth::requireLogin();
        require_csrf();
        $testId = (int) input('test_id', 0);
        try {
            $scoreRaw = input('score', '');
            $score = ($scoreRaw === '' || $scoreRaw === null) ? null : (float) $scoreRaw;
            EvaluationService::create(
                Auth::id(),
                $testId,
                (string) input('verdict', 'pending'),
                $score,
                (string) input('notes', ''),
                (string) input('model_label', ''),
                (string) input('output_sample', ''),
                int_or_null(input('entry_version_id'))
            );
            flash('success', 'Evaluación registrada.');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
        }
        redirect('/pruebas/' . $testId);
    }

    public static function destroy(string $id): void
    {
        Auth::requireLogin();
        require_csrf();
        $ev = EvaluationService::find((int) $id, Auth::id());
        $testId = $ev ? (int) $ev['test_id'] : 0;
        EvaluationService::delete((int) $id, Auth::id());
        flash('success', 'Evaluación eliminada.');
        redirect($testId > 0 ? '/pruebas/' . $testId : '/evaluaciones');
    }
}
