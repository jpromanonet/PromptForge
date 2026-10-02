<?php
/** @var array|null $test */
/** @var int|null $entryId */
/** @var array $entries */
?>
<section class="page-head">
    <div>
        <p class="eyebrow">Prueba</p>
        <h1 class="page-title"><?= e($test ? 'Editar prueba' : 'Nueva prueba') ?></h1>
    </div>
    <a class="btn" href="<?= e(url($test ? '/pruebas/' . $test['id'] : '/pruebas')) ?>">Volver</a>
</section>

<section class="panel">
    <form class="stack-form" method="post" action="<?= e(url($test ? '/pruebas/' . $test['id'] : '/pruebas')) ?>">
        <?= csrf_field() ?>
        <label class="field">
            <span>Entrada</span>
            <?php if ($test): ?>
                <input type="text" value="<?= e(($test['entry_title'] ?? '') . ' (' . entry_type_label($test['entry_type']) . ')') ?>" disabled>
            <?php else: ?>
                <select name="entry_id" required>
                    <option value="">— Elegir —</option>
                    <?php foreach ($entries as $row): ?>
                        <option value="<?= (int) $row['id'] ?>" <?= (int) $entryId === (int) $row['id'] ? 'selected' : '' ?>>
                            <?= e(entry_type_label($row['type']) . ' · ' . $row['title']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
        </label>
        <label class="field">
            <span>Nombre</span>
            <input type="text" name="name" required value="<?= e($test['name'] ?? '') ?>">
        </label>
        <label class="field">
            <span>Input de prueba</span>
            <textarea name="input_text" rows="5" class="code-area"><?= e($test['input_text'] ?? '') ?></textarea>
        </label>
        <label class="field">
            <span>Salida esperada</span>
            <textarea name="expected_output" rows="5" class="code-area"><?= e($test['expected_output'] ?? '') ?></textarea>
        </label>
        <label class="field">
            <span>Criterios</span>
            <textarea name="criteria" rows="3"><?= e($test['criteria'] ?? '') ?></textarea>
        </label>
        <?php if ($test): ?>
            <label class="field">
                <span>Activa</span>
                <select name="is_active">
                    <option value="1" <?= (int) $test['is_active'] === 1 ? 'selected' : '' ?>>Sí</option>
                    <option value="0" <?= (int) $test['is_active'] === 0 ? 'selected' : '' ?>>No</option>
                </select>
            </label>
        <?php endif; ?>
        <div class="form-actions">
            <button class="btn btn-accent" type="submit">Guardar</button>
        </div>
    </form>
</section>
