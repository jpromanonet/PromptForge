<?php /** @var array|null $library */ ?>
<section class="page-head">
    <div>
        <p class="eyebrow">Biblioteca</p>
        <h1 class="page-title"><?= e($library ? 'Editar biblioteca' : 'Nueva biblioteca') ?></h1>
    </div>
    <a class="btn" href="<?= e(url($library ? '/bibliotecas/' . $library['id'] : '/bibliotecas')) ?>">Volver</a>
</section>

<section class="panel">
    <form class="stack-form" method="post" action="<?= e(url($library ? '/bibliotecas/' . $library['id'] : '/bibliotecas')) ?>">
        <?= csrf_field() ?>
        <label class="field">
            <span>Nombre</span>
            <input type="text" name="name" required value="<?= e($library['name'] ?? '') ?>" maxlength="160">
        </label>
        <label class="field">
            <span>Descripción</span>
            <textarea name="description" rows="3"><?= e($library['description'] ?? '') ?></textarea>
        </label>
        <label class="field">
            <span>Color</span>
            <input type="color" name="color" value="<?= e($library['color'] ?? '#0E9AA7') ?>">
        </label>
        <div class="form-actions">
            <button class="btn btn-accent" type="submit">Guardar</button>
        </div>
    </form>
</section>
