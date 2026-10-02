<section class="page-head">
    <div>
        <p class="eyebrow">Taller</p>
        <h1 class="page-title">Panel</h1>
        <p class="lede">Estado de tu biblioteca: prompts, pruebas y evaluaciones recientes.</p>
    </div>
    <a class="btn btn-accent" href="<?= e(url('/prompts/nuevo')) ?>"><?= icon('add', 16) ?> Nuevo prompt</a>
</section>

<section class="metric-grid metric-grid-6">
    <article class="metric-tile accent-teal"><span class="metric-label">Prompts</span><strong class="metric-value"><?= format_number($stats['prompts']) ?></strong></article>
    <article class="metric-tile accent-steel"><span class="metric-label">Instrucciones</span><strong class="metric-value"><?= format_number($stats['instructions']) ?></strong></article>
    <article class="metric-tile accent-ember"><span class="metric-label">Plantillas</span><strong class="metric-value"><?= format_number($stats['templates']) ?></strong></article>
    <article class="metric-tile"><span class="metric-label">Bibliotecas</span><strong class="metric-value"><?= format_number($stats['libraries']) ?></strong></article>
    <article class="metric-tile accent-moss"><span class="metric-label">Pruebas</span><strong class="metric-value"><?= format_number($stats['tests']) ?></strong></article>
    <article class="metric-tile accent-crimson"><span class="metric-label">Pass rate</span><strong class="metric-value"><?= $stats['pass_rate'] === null ? '—' : e((string) $stats['pass_rate']) . '%' ?></strong></article>
</section>

<div class="split-panels">
    <section class="panel">
        <div class="panel-head">
            <h2>Entradas recientes</h2>
            <a href="<?= e(url('/prompts')) ?>">Ver prompts</a>
        </div>
        <?php if (empty($stats['recent_entries'])): ?>
            <p class="empty">Todavía no hay entradas. Creá el primer prompt para empezar.</p>
        <?php else: ?>
            <ul class="entity-list">
                <?php foreach ($stats['recent_entries'] as $row): ?>
                    <li>
                        <a class="entity-row" href="<?= e(url(entry_type_path($row['type']) . '/' . $row['id'])) ?>">
                            <div>
                                <strong><?= e($row['title']) ?></strong>
                                <span class="muted"><?= e(entry_type_label($row['type'])) ?> · v<?= (int) $row['current_version'] ?> · <?= e(format_dt($row['updated_at'])) ?></span>
                            </div>
                            <span class="badge <?= e(status_badge_class($row['status'])) ?>"><?= e(entry_status_label($row['status'])) ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

    <section class="panel">
        <div class="panel-head">
            <h2>Últimas evaluaciones</h2>
            <a href="<?= e(url('/evaluaciones')) ?>">Ver todas</a>
        </div>
        <?php if (empty($stats['recent_evaluations'])): ?>
            <p class="empty">Sin evaluaciones todavía.</p>
        <?php else: ?>
            <ul class="entity-list">
                <?php foreach ($stats['recent_evaluations'] as $ev): ?>
                    <li>
                        <div class="entity-row static">
                            <div>
                                <strong><?= e($ev['test_name']) ?></strong>
                                <span class="muted"><?= e($ev['entry_title']) ?> · <?= e(format_dt($ev['created_at'])) ?></span>
                            </div>
                            <span class="badge <?= e(status_badge_class($ev['verdict'])) ?>"><?= e(evaluation_verdicts()[$ev['verdict']] ?? $ev['verdict']) ?></span>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
</div>
