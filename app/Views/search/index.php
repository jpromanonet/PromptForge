<section class="page-head">
    <div>
        <p class="eyebrow">Explorar</p>
        <h1 class="page-title">Buscar</h1>
        <p class="lede">Encontrá entradas, cuerpos versionados, pruebas y bibliotecas.</p>
    </div>
</section>

<section class="panel">
    <form method="get" action="<?= e(url('/buscar')) ?>" class="stack-form">
        <label class="field">
            <span>Consulta</span>
            <input type="search" name="q" value="<?= e($q) ?>" placeholder="ej. onboarding, {{cliente}}, support…" autofocus>
        </label>
        <button class="btn btn-accent" type="submit">Buscar</button>
    </form>
</section>

<?php if ($q !== ''): ?>
    <section class="panel">
        <div class="panel-head"><h2>Entradas</h2></div>
        <?php if (empty($results['entries'])): ?>
            <p class="empty">Sin coincidencias.</p>
        <?php else: ?>
            <ul class="entity-list">
                <?php foreach ($results['entries'] as $row): ?>
                    <li>
                        <a class="entity-row" href="<?= e(url(entry_type_path($row['type']) . '/' . $row['id'])) ?>">
                            <div>
                                <strong><?= e($row['title']) ?></strong>
                                <span class="muted"><?= e(entry_type_label($row['type'])) ?> · v<?= (int) $row['current_version'] ?></span>
                            </div>
                            <span class="badge <?= e(status_badge_class($row['status'])) ?>"><?= e(entry_status_label($row['status'])) ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

    <section class="panel">
        <div class="panel-head"><h2>Pruebas</h2></div>
        <?php if (empty($results['tests'])): ?>
            <p class="empty">Sin coincidencias.</p>
        <?php else: ?>
            <ul class="entity-list">
                <?php foreach ($results['tests'] as $t): ?>
                    <li>
                        <a class="entity-row" href="<?= e(url('/pruebas/' . $t['id'])) ?>">
                            <div>
                                <strong><?= e($t['name']) ?></strong>
                                <span class="muted"><?= e(entry_type_label($t['entry_type'])) ?> · <?= e($t['entry_title']) ?></span>
                            </div>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

    <section class="panel">
        <div class="panel-head"><h2>Bibliotecas</h2></div>
        <?php if (empty($results['libraries'])): ?>
            <p class="empty">Sin coincidencias.</p>
        <?php else: ?>
            <ul class="entity-list">
                <?php foreach ($results['libraries'] as $lib): ?>
                    <li>
                        <a class="entity-row" href="<?= e(url('/bibliotecas/' . $lib['id'])) ?>">
                            <div>
                                <strong><?= e($lib['name']) ?></strong>
                                <span class="muted"><?= e(truncate($lib['description'] ?? '', 80)) ?></span>
                            </div>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
<?php endif; ?>
