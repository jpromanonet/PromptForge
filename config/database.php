<?php

declare(strict_types=1);

require_once __DIR__ . '/env.php';

return [
    'host' => pf_env('DB_HOST', '127.0.0.1') ?? '127.0.0.1',
    'port' => (int) (pf_env('DB_PORT', '3306') ?? '3306'),
    'name' => pf_env('DB_NAME', 'promptforge') ?? 'promptforge',
    'user' => pf_env('DB_USER', 'root') ?? 'root',
    'pass' => pf_env('DB_PASS', '') ?? '',
    'charset' => 'utf8mb4',
];
