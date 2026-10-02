<section class="page-head">
    <div>
        <p class="eyebrow">Calidad</p>
        <h1 class="page-title">Evaluaciones</h1>
        <p class="lede">Resultados recientes de tus pruebas.</p>
    </div>
    <a class="btn" href="<?= e(url('/pruebas')) ?>">Ir a pruebas</a>
</section>

<?php if (empty($evaluations)): ?>
    <section class="panel"><p class="empty">Todavía no hay evaluaciones.</p></section>
<?php else: ?>
    <ul class="entity-list panel">
        <?php foreach ($evaluations as $ev): ?>
            <li>
                <a class="entity-row" href="<?= e(url('/pruebas/' . $ev['test_id'])) ?>">
                    <div>
                        <strong><?= e($ev['test_name']) ?></strong>
                        <span class="muted">
                            <?= e($ev['entry_title']) ?>
                            <?= !empty($ev['version_number']) ? ' · v' . (int) $ev['version_number'] : '' ?>
                            <?= $ev['score'] !== null ? ' · score ' . e((string) $ev['score']) : '' ?>
                            · <?= e(format_dt($ev['created_at'])) ?>
                        </span>
                    </div>
                    <span class="badge <?= e(status_badge_class($ev['verdict'])) ?>"><?= e(evaluation_verdicts()[$ev['verdict']] ?? $ev['verdict']) ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
