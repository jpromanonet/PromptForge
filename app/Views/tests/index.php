<section class="page-head">
    <div>
        <p class="eyebrow">Calidad</p>
        <h1 class="page-title">Pruebas</h1>
        <p class="lede">Casos de prueba asociados a prompts, instrucciones o plantillas.</p>
    </div>
    <a class="btn btn-accent" href="<?= e(url('/pruebas/nueva')) ?>"><?= icon('add', 16) ?> Nueva prueba</a>
</section>

<?php if (empty($tests)): ?>
    <section class="panel"><p class="empty">Todavía no hay pruebas.</p></section>
<?php else: ?>
    <ul class="entity-list panel">
        <?php foreach ($tests as $t): ?>
            <li>
                <a class="entity-row" href="<?= e(url('/pruebas/' . $t['id'])) ?>">
                    <div>
                        <strong><?= e($t['name']) ?></strong>
                        <span class="muted"><?= e(entry_type_label($t['entry_type'])) ?> · <?= e($t['entry_title']) ?> · <?= (int) $t['eval_count'] ?> evals</span>
                    </div>
                    <?php if (!empty($t['last_verdict'])): ?>
                        <span class="badge <?= e(status_badge_class($t['last_verdict'])) ?>"><?= e(evaluation_verdicts()[$t['last_verdict']] ?? $t['last_verdict']) ?></span>
                    <?php elseif (!(int) $t['is_active']): ?>
                        <span class="badge badge-steel">Inactiva</span>
                    <?php endif; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
