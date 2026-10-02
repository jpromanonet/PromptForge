<?php
/** @var string $templateFile */
/** @var string $appName */
/** @var string|null $title */
$flashes = take_flashes();
?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(($title ?? 'Acceso') . ' · ' . $appName) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(url('/assets/css/app.css')) ?>">
    <link rel="icon" href="<?= e(url('/assets/icons/forge.svg')) ?>" type="image/svg+xml">
</head>
<body class="auth-body">
<?php if ($flashes): ?>
    <div class="auth-flash-wrap">
        <?php foreach ($flashes as $flash): ?>
            <div class="flash flash-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php require $templateFile; ?>
</body>
</html>
