<?php
/** @var string $type */
/** @var array $entry */
/** @var array|null $version */
/** @var array $versions */
/** @var array $tests */
$base = entry_type_path($type);
$vars = [];
if (!empty($version['variables_json'])) {
    $decoded = json_decode((string) $version['variables_json'], true);
    $vars = is_array($decoded) ? $decoded : [];
}
?>
<section class="page-head">
    <div>
        <p class="eyebrow"><?= e(entry_type_label($type)) ?> · v<?= (int) $entry['current_version'] ?></p>
        <h1 class="page-title"><?= e($entry['title']) ?></h1>
        <p class="lede"><?= e($entry['description'] ?: 'Sin descripción') ?></p>
        <div class="chip-row">
            <span class="badge <?= e(status_badge_class($entry['status'])) ?>"><?= e(entry_status_label($entry['status'])) ?></span>
            <?php if (!empty($entry['library_name'])): ?>
                <span class="badge badge-steel"><?= e($entry['library_name']) ?></span>
            <?php endif; ?>
            <?php foreach (parse_tags($entry['tags'] ?? null) as $tag): ?>
                <span class="badge badge-ember"><?= e($tag) ?></span>
            <?php endforeach; ?>
            <?php if (!empty($entry['model_hint'])): ?>
                <span class="badge badge-teal"><?= e($entry['model_hint']) ?></span>
            <?php endif; ?>
        </div>
    </div>
    <div class="page-actions">
        <a class="btn" href="<?= e(url($base . '/' . $entry['id'] . '/editar')) ?>"><?= icon('edit', 14) ?> Metadatos</a>
        <a class="btn btn-accent" href="<?= e(url($base . '/' . $entry['id'] . '/nueva-version')) ?>"><?= icon('add', 14) ?> Nueva versión</a>
        <a class="btn" href="<?= e(url($base . '/' . $entry['id'] . '/exportar?format=md')) ?>">Export MD</a>
        <a class="btn" href="<?= e(url($base . '/' . $entry['id'] . '/exportar?format=json')) ?>">Export JSON</a>
    </div>
</section>

<section class="panel">
    <div class="panel-head"><h2>Cuerpo actual</h2></div>
    <?php if (!$version): ?>
        <p class="empty">Sin versión cargada.</p>
    <?php else: ?>
        <?php if ($vars): ?>
            <p class="muted">Variables detectadas: <?= e(implode(', ', array_map(fn ($v) => '{{' . $v . '}}', $vars))) ?></p>
        <?php endif; ?>
        <pre class="code-block"><?= e($version['body']) ?></pre>
        <?php if (!empty($version['changelog'])): ?>
            <p class="muted">Changelog: <?= e($version['changelog']) ?></p>
        <?php endif; ?>
    <?php endif; ?>
</section>

<div class="split-panels">
    <section class="panel">
        <div class="panel-head"><h2>Historial</h2></div>
        <?php if (empty($versions)): ?>
            <p class="empty">Sin versiones.</p>
        <?php else: ?>
            <ul class="entity-list">
                <?php foreach ($versions as $v): ?>
                    <li>
                        <div class="entity-row static">
                            <div>
                                <strong>v<?= (int) $v['version'] ?><?= (int) $v['version'] === (int) $entry['current_version'] ? ' · actual' : '' ?></strong>
                                <span class="muted"><?= e($v['changelog'] ?: 'Sin notas') ?> · <?= e(format_dt($v['created_at'])) ?></span>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

    <section class="panel">
        <div class="panel-head">
            <h2>Pruebas</h2>
            <a class="btn btn-sm" href="<?= e(url('/pruebas/nueva?entry_id=' . $entry['id'])) ?>">+ Prueba</a>
        </div>
        <?php if (empty($tests)): ?>
            <p class="empty">Todavía no hay pruebas asociadas.</p>
        <?php else: ?>
            <ul class="entity-list">
                <?php foreach ($tests as $t): ?>
                    <li>
                        <a class="entity-row" href="<?= e(url('/pruebas/' . $t['id'])) ?>">
                            <div>
                                <strong><?= e($t['name']) ?></strong>
                                <span class="muted"><?= (int) $t['eval_count'] ?> evaluaciones</span>
                            </div>
                            <?php if (!empty($t['last_verdict'])): ?>
                                <span class="badge <?= e(status_badge_class($t['last_verdict'])) ?>"><?= e(evaluation_verdicts()[$t['last_verdict']] ?? $t['last_verdict']) ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
</div>

<section class="panel">
    <form method="post" action="<?= e(url($base . '/' . $entry['id'] . '/eliminar')) ?>" onsubmit="return confirm('¿Eliminar esta entrada y todo su historial?');">
        <?= csrf_field() ?>
        <button class="btn btn-danger" type="submit"><?= icon('delete', 14) ?> Eliminar</button>
    </form>
</section>
