<?php
/** @var array $test */
/** @var array|null $entry */
/** @var array $evaluations */
/** @var array $versions */
?>
<section class="page-head">
    <div>
        <p class="eyebrow">Prueba · <?= e(entry_type_label($test['entry_type'])) ?></p>
        <h1 class="page-title"><?= e($test['name']) ?></h1>
        <p class="lede">
            Sobre
            <?php if ($entry): ?>
                <a href="<?= e(url(entry_type_path($entry['type']) . '/' . $entry['id'])) ?>"><?= e($entry['title']) ?></a>
            <?php else: ?>
                <?= e($test['entry_title']) ?>
            <?php endif; ?>
        </p>
    </div>
    <div class="page-actions">
        <a class="btn" href="<?= e(url('/pruebas/' . $test['id'] . '/editar')) ?>"><?= icon('edit', 14) ?> Editar</a>
        <a class="btn" href="<?= e(url('/pruebas')) ?>">Volver</a>
    </div>
</section>

<div class="split-panels">
    <section class="panel">
        <h2>Caso</h2>
        <p class="form-section-title">Input</p>
        <pre class="code-block"><?= e($test['input_text'] ?: '—') ?></pre>
        <p class="form-section-title">Esperado</p>
        <pre class="code-block"><?= e($test['expected_output'] ?: '—') ?></pre>
        <?php if (!empty($test['criteria'])): ?>
            <p class="form-section-title">Criterios</p>
            <p><?= e($test['criteria']) ?></p>
        <?php endif; ?>
    </section>

    <section class="panel">
        <h2>Registrar evaluación</h2>
        <form class="stack-form" method="post" action="<?= e(url('/evaluaciones')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="test_id" value="<?= (int) $test['id'] ?>">
            <label class="field">
                <span>Versión evaluada</span>
                <select name="entry_version_id">
                    <option value="">Versión actual</option>
                    <?php foreach ($versions as $v): ?>
                        <option value="<?= (int) $v['id'] ?>">v<?= (int) $v['version'] ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <div class="form-grid-2">
                <label class="field">
                    <span>Veredicto</span>
                    <select name="verdict" required>
                        <?php foreach (evaluation_verdicts() as $k => $label): ?>
                            <option value="<?= e($k) ?>" <?= $k === 'pass' ? 'selected' : '' ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="field">
                    <span>Score (0–100)</span>
                    <input type="number" name="score" min="0" max="100" step="0.1" placeholder="85">
                </label>
            </div>
            <label class="field">
                <span>Modelo usado</span>
                <input type="text" name="model_label" placeholder="modelo / proveedor">
            </label>
            <label class="field">
                <span>Salida observada</span>
                <textarea name="output_sample" rows="4" class="code-area"></textarea>
            </label>
            <label class="field">
                <span>Notas</span>
                <textarea name="notes" rows="3"></textarea>
            </label>
            <button class="btn btn-accent" type="submit">Guardar evaluación</button>
        </form>
    </section>
</div>

<section class="panel">
    <div class="panel-head"><h2>Historial de evaluaciones</h2></div>
    <?php if (empty($evaluations)): ?>
        <p class="empty">Sin evaluaciones todavía.</p>
    <?php else: ?>
        <ul class="entity-list">
            <?php foreach ($evaluations as $ev): ?>
                <li>
                    <div class="entity-row static">
                        <div>
                            <strong><?= e(evaluation_verdicts()[$ev['verdict']] ?? $ev['verdict']) ?><?= $ev['score'] !== null ? ' · ' . e((string) $ev['score']) : '' ?></strong>
                            <span class="muted">
                                <?= !empty($ev['version_number']) ? 'v' . (int) $ev['version_number'] . ' · ' : '' ?>
                                <?= e($ev['model_label'] ?: 'sin modelo') ?> ·
                                <?= e(format_dt($ev['created_at'])) ?>
                                <?= $ev['notes'] ? ' · ' . e(truncate($ev['notes'], 80)) : '' ?>
                            </span>
                        </div>
                        <form method="post" action="<?= e(url('/evaluaciones/' . $ev['id'] . '/eliminar')) ?>" onsubmit="return confirm('¿Eliminar evaluación?');">
                            <?= csrf_field() ?>
                            <button class="btn btn-sm btn-ghost" type="submit"><?= icon('delete', 14) ?></button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>

<section class="panel">
    <form method="post" action="<?= e(url('/pruebas/' . $test['id'] . '/eliminar')) ?>" onsubmit="return confirm('¿Eliminar esta prueba y sus evaluaciones?');">
        <?= csrf_field() ?>
        <button class="btn btn-danger" type="submit"><?= icon('delete', 14) ?> Eliminar prueba</button>
    </form>
</section>
