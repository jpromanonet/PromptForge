<?php
/** @var array $library */
/** @var array $entries */
?>
<section class="page-head">
    <div>
        <p class="eyebrow">Biblioteca</p>
        <h1 class="page-title"><?= e($library['name']) ?></h1>
        <p class="lede"><?= e($library['description'] ?: 'Sin descripción') ?></p>
    </div>
    <div class="page-actions">
        <a class="btn" href="<?= e(url('/bibliotecas/' . $library['id'] . '/editar')) ?>"><?= icon('edit', 14) ?> Editar</a>
        <a class="btn" href="<?= e(url('/bibliotecas')) ?>">Volver</a>
    </div>
</section>

<?php foreach (entry_types() as $type => $label): ?>
    <section class="panel">
        <div class="panel-head">
            <h2><?= e($label) ?>s</h2>
            <a href="<?= e(url(entry_type_path($type) . '/nuevo')) ?>">+ Nuevo</a>
        </div>
        <?php if (empty($entries[$type])): ?>
            <p class="empty">Vacío.</p>
        <?php else: ?>
            <ul class="entity-list">
                <?php foreach ($entries[$type] as $row): ?>
                    <li>
                        <a class="entity-row" href="<?= e(url(entry_type_path($type) . '/' . $row['id'])) ?>">
                            <div>
                                <strong><?= e($row['title']) ?></strong>
                                <span class="muted">v<?= (int) $row['current_version'] ?></span>
                            </div>
                            <span class="badge <?= e(status_badge_class($row['status'])) ?>"><?= e(entry_status_label($row['status'])) ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
<?php endforeach; ?>

<section class="panel">
    <form method="post" action="<?= e(url('/bibliotecas/' . $library['id'] . '/eliminar')) ?>" onsubmit="return confirm('¿Eliminar la biblioteca? Las entradas quedan sin biblioteca.');">
        <?= csrf_field() ?>
        <button class="btn btn-danger" type="submit"><?= icon('delete', 14) ?> Eliminar biblioteca</button>
    </form>
</section>
