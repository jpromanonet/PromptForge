<?php
/** @var string $type */
/** @var array|null $entry */
/** @var array|null $version */
/** @var array $libraries */
/** @var string $mode */
$base = entry_type_path($type);
$isCreate = $mode === 'create';
$isVersion = $mode === 'version';
$action = $isCreate
    ? url($base)
    : ($isVersion ? url($base . '/' . $entry['id'] . '/nueva-version') : url($base . '/' . $entry['id']));
?>
<section class="page-head">
    <div>
        <p class="eyebrow"><?= e(entry_type_label($type)) ?></p>
        <h1 class="page-title"><?= e($title) ?></h1>
        <p class="lede">
            <?php if ($isVersion): ?>
                Se crea una nueva versión (v<?= (int) $entry['current_version'] + 1 ?>). El historial anterior se conserva.
            <?php elseif ($isCreate): ?>
                Usá <code>{{variable}}</code> para marcar placeholders en plantillas y prompts.
            <?php else: ?>
                Editá título, estado, tags y biblioteca. El cuerpo se versiona por separado.
            <?php endif; ?>
        </p>
    </div>
    <a class="btn" href="<?= e(url($entry ? $base . '/' . $entry['id'] : $base)) ?>">Volver</a>
</section>

<section class="panel">
    <form method="post" action="<?= e($action) ?>" class="stack-form">
        <?= csrf_field() ?>

        <?php if (!$isVersion): ?>
            <div class="form-grid-2">
                <label class="field">
                    <span>Título</span>
                    <input type="text" name="title" required value="<?= e($entry['title'] ?? '') ?>" maxlength="220">
                </label>
                <label class="field">
                    <span>Estado</span>
                    <select name="status">
                        <?php foreach (entry_statuses() as $k => $label): ?>
                            <option value="<?= e($k) ?>" <?= ($entry['status'] ?? 'draft') === $k ? 'selected' : '' ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="field">
                    <span>Biblioteca</span>
                    <select name="library_id">
                        <option value="">— Sin biblioteca —</option>
                        <?php foreach ($libraries as $lib): ?>
                            <option value="<?= (int) $lib['id'] ?>" <?= (int) ($entry['library_id'] ?? 0) === (int) $lib['id'] ? 'selected' : '' ?>><?= e($lib['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="field">
                    <span>Modelo sugerido</span>
                    <input type="text" name="model_hint" value="<?= e($entry['model_hint'] ?? '') ?>" placeholder="gpt-4.1, claude, local…">
                </label>
            </div>
            <label class="field">
                <span>Descripción</span>
                <textarea name="description" rows="3"><?= e($entry['description'] ?? '') ?></textarea>
            </label>
            <label class="field">
                <span>Tags (separados por coma)</span>
                <input type="text" name="tags" value="<?= e($entry['tags'] ?? '') ?>" placeholder="seo, support, es">
            </label>
        <?php endif; ?>

        <?php if ($isCreate || $isVersion): ?>
            <label class="field">
                <span>Cuerpo</span>
                <textarea name="body" rows="14" class="code-area" required><?= e($version['body'] ?? '') ?></textarea>
            </label>
            <label class="field">
                <span>Changelog de versión</span>
                <input type="text" name="changelog" value="" placeholder="<?= e($isCreate ? 'Versión inicial' : 'Qué cambió en esta versión') ?>">
            </label>
        <?php endif; ?>

        <div class="form-actions">
            <button class="btn btn-accent" type="submit"><?= $isVersion ? 'Guardar versión' : ($isCreate ? 'Crear' : 'Guardar') ?></button>
            <a class="btn btn-ghost" href="<?= e(url($entry ? $base . '/' . $entry['id'] : $base)) ?>">Cancelar</a>
        </div>
    </form>
</section>
