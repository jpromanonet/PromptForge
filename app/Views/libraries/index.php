<section class="page-head">
    <div>
        <p class="eyebrow">Organización</p>
        <h1 class="page-title">Bibliotecas</h1>
        <p class="lede">Agrupá prompts, instrucciones y plantillas por proyecto o dominio.</p>
    </div>
    <a class="btn btn-accent" href="<?= e(url('/bibliotecas/nueva')) ?>"><?= icon('add', 16) ?> Nueva biblioteca</a>
</section>

<?php if (empty($libraries)): ?>
    <section class="panel"><p class="empty">Todavía no hay bibliotecas.</p></section>
<?php else: ?>
    <div class="project-grid">
        <?php foreach ($libraries as $lib): ?>
            <a class="project-card" href="<?= e(url('/bibliotecas/' . $lib['id'])) ?>">
                <div class="project-card-top">
                    <span class="color-swatch" style="background:<?= e($lib['color']) ?>"></span>
                    <span><?= (int) $lib['entry_count'] ?> entradas</span>
                </div>
                <h2><?= e($lib['name']) ?></h2>
                <p><?= e(truncate($lib['description'] ?? '', 120) ?: 'Sin descripción') ?></p>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
