<?php

declare(strict_types=1);

require_once __DIR__ . '/config/env.php';

header('Content-Type: text/html; charset=utf-8');

$host = pf_env('DB_HOST', '127.0.0.1');
$port = (int) pf_env('DB_PORT', '3306');
$name = pf_env('DB_NAME', 'promptforge');
$user = pf_env('DB_USER', 'root');
$pass = pf_env('DB_PASS', '') ?? '';
$sqlFile = __DIR__ . '/sql/schema.sql';

$ok = false;
$message = null;

try {
    if (!is_file($sqlFile)) {
        throw new RuntimeException('No está sql/schema.sql');
    }
    $pdo = new PDO(
        sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $host, $port),
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $safeName = str_replace('`', '', (string) $name);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$safeName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `{$safeName}`");

    $tables = $pdo->query("SHOW TABLES LIKE 'users'")->fetch();
    if (!$tables) {
        $sql = (string) file_get_contents($sqlFile);
        foreach (preg_split('/;\s*\n/', $sql) as $stmt) {
            $stmt = trim($stmt);
            if ($stmt === '' || str_starts_with($stmt, '--')) {
                continue;
            }
            if (preg_match('/^(SET|CREATE DATABASE|USE)\b/i', $stmt)) {
                continue;
            }
            $pdo->exec($stmt);
        }
        $message = 'Base creada e importada.';
    } else {
        $message = 'La base ya existía.';
    }
    $ok = true;
} catch (Throwable $e) {
    $ok = false;
    $message = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Instalar PromptForge</title>
    <style>
        body { font-family: "DM Sans", system-ui, sans-serif; background:#E8F0F4; color:#0B1520; max-width:42rem; margin:3rem auto; padding:1rem; }
        .window { background:#F7FBFC; border:2px solid #0B1520; box-shadow:6px 6px 0 rgba(11,21,32,.12); padding:1.25rem; }
        .ok { color:#1F7A5C; } .err { color:#C23B3B; }
        .btn { border:2px solid #0B1520; background:#0E9AA7; padding:.5rem .75rem; box-shadow:3px 3px 0 #0B1520; cursor:pointer; text-decoration:none; color:#fff; display:inline-block; font-weight:700; font-family:inherit; }
        h1 { font-family: Syne, system-ui, sans-serif; letter-spacing:-.02em; }
    </style>
</head>
<body>
<div class="window">
    <h1>PROMPTFORGE INSTALL</h1>
    <?php if ($ok): ?>
        <p class="ok"><?= htmlspecialchars((string) $message, ENT_QUOTES, 'UTF-8') ?></p>
        <p><a class="btn" href="index.php">Entrar a la forja</a></p>
    <?php else: ?>
        <p class="err"><?= htmlspecialchars((string) $message, ENT_QUOTES, 'UTF-8') ?></p>
        <p>Revisá <code>.env</code> (copiá desde <code>.env.example</code>).</p>
    <?php endif; ?>
</div>
</body>
</html>
