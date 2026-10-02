<?php

declare(strict_types=1);

require_once __DIR__ . '/env.php';

return [
    'name' => pf_env('APP_NAME', 'PromptForge') ?? 'PromptForge',
    'env' => pf_env('APP_ENV', 'local') ?? 'local',
    'debug' => filter_var(pf_env('APP_DEBUG', 'true'), FILTER_VALIDATE_BOOLEAN),
    'url' => pf_env('APP_URL', '') ?? '',
    'timezone' => 'America/Argentina/Buenos_Aires',
    'session_name' => 'promptforge_session',
    'session_lifetime' => 86400,
    'session_idle' => 86400,
    'version' => '0.1.0',
];
