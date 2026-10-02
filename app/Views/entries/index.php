<?php
/** @var string $type */
/** @var array $entries */
/** @var array $libraries */
/** @var array $filters */
$base = entry_type_path($type);
$singular = entry_type_label($type);
?>
<section class="page-head">
    <div>
        <p class="eyebrow">Biblioteca</p>
        <h1 class="page-title"><?= e($singular) ?>s</h1>
        <p class="lede">Entradas versionadas de tipo <?= e(mb_strtolower($singular)) ?>.</p>
    </div>
    <a class="btn btn-accent" href="<?= e(url($base . '/nuevo')) ?>"><?= icon('add', 16) ?> Nuevo</a>
</section>

<form class="filter-bar" method="get" action="<?= e(url($base)) ?>">
    <label>
        <span>Buscar</span>
        <input type="search" name="q" value="<?= e($filters['q'] ?? '') ?>" placeholder="Título, tags…">
    </label>
    <label>
        <span>Estado</span>
        <select name="status">
            <option value="">Todos</option>
            <?php foreach (entry_statuses() as $k => $label): ?>
                <option value="<?= e($k) ?>" <?= ($filters['status'] ?? '') === $k ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>
        <span>Biblioteca</span>
        <select name="library_id">
            <option value="">Todas</option>
            <?php foreach ($libraries as $lib): ?>
                <option value="<?= (int) $lib['id'] ?>" <?= (int) ($filters['library_id'] ?? 0) === (int) $lib['id'] ? 'selected' : '' ?>><?= e($lib['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <button class="btn" type="submit">Filtrar</button>
</form>

<?php if (empty($entries)): ?>
    <section class="panel"><p class="empty">No hay <?= e(mb_strtolower($singular)) ?>s todavía.</p></section>
<?php else: ?>
    <div class="project-grid">
        <?php foreach ($entries as $row): ?>
            <a class="project-card" href="<?= e(url($base . '/' . $row['id'])) ?>">
                <div class="project-card-top">
                    <span class="badge <?= e(status_badge_class($row['status'])) ?>"><?= e(entry_status_label($row['status'])) ?></span>
                    <span>v<?= (int) $row['current_version'] ?></span>
                </div>
                <h2><?= e($row['title']) ?></h2>
                <p><?= e(truncate($row['description'] ?? '', 110) ?: 'Sin descripción') ?></p>
                <div class="project-card-meta">
                    <span><?= e($row['library_name'] ?? 'Sin biblioteca') ?></span>
                    <span><?= (int) ($row['test_count'] ?? 0) ?> pruebas</span>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
