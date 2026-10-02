<?php

declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';

$router = new Router();

$router->get('/', [HomeController::class, 'index']);

$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/registro', [AuthController::class, 'showRegister']);
$router->post('/registro', [AuthController::class, 'register']);
$router->post('/logout', [AuthController::class, 'logout']);

$router->get('/panel', [DashboardController::class, 'index']);
$router->get('/buscar', [SearchController::class, 'index']);
$router->get('/configuracion', [SettingsController::class, 'index']);
$router->post('/configuracion', [SettingsController::class, 'save']);
$router->post('/configuracion/password', [SettingsController::class, 'password']);
$router->post('/configuracion/avatar', [SettingsController::class, 'avatar']);
$router->post('/configuracion/avatar/eliminar', [SettingsController::class, 'avatarRemove']);
$router->post('/configuracion/tema', [SettingsController::class, 'theme']);

$router->get('/bibliotecas', [LibraryController::class, 'index']);
$router->get('/bibliotecas/nueva', [LibraryController::class, 'create']);
$router->post('/bibliotecas', [LibraryController::class, 'store']);
$router->get('/bibliotecas/{id}', [LibraryController::class, 'show']);
$router->get('/bibliotecas/{id}/editar', [LibraryController::class, 'edit']);
$router->post('/bibliotecas/{id}', [LibraryController::class, 'update']);
$router->post('/bibliotecas/{id}/eliminar', [LibraryController::class, 'destroy']);

$router->get('/prompts', [EntryController::class, 'indexPrompts']);
$router->get('/prompts/nuevo', fn () => EntryController::create('prompt'));
$router->post('/prompts', fn () => EntryController::store('prompt'));
$router->get('/prompts/{id}', fn ($id) => EntryController::show('prompt', $id));
$router->get('/prompts/{id}/editar', fn ($id) => EntryController::edit('prompt', $id));
$router->post('/prompts/{id}', fn ($id) => EntryController::update('prompt', $id));
$router->get('/prompts/{id}/nueva-version', fn ($id) => EntryController::newVersionForm('prompt', $id));
$router->post('/prompts/{id}/nueva-version', fn ($id) => EntryController::storeVersion('prompt', $id));
$router->post('/prompts/{id}/eliminar', fn ($id) => EntryController::destroy('prompt', $id));
$router->get('/prompts/{id}/exportar', fn ($id) => EntryController::export('prompt', $id));

$router->get('/instrucciones', [EntryController::class, 'indexInstructions']);
$router->get('/instrucciones/nuevo', fn () => EntryController::create('instruction'));
$router->post('/instrucciones', fn () => EntryController::store('instruction'));
$router->get('/instrucciones/{id}', fn ($id) => EntryController::show('instruction', $id));
$router->get('/instrucciones/{id}/editar', fn ($id) => EntryController::edit('instruction', $id));
$router->post('/instrucciones/{id}', fn ($id) => EntryController::update('instruction', $id));
$router->get('/instrucciones/{id}/nueva-version', fn ($id) => EntryController::newVersionForm('instruction', $id));
$router->post('/instrucciones/{id}/nueva-version', fn ($id) => EntryController::storeVersion('instruction', $id));
$router->post('/instrucciones/{id}/eliminar', fn ($id) => EntryController::destroy('instruction', $id));
$router->get('/instrucciones/{id}/exportar', fn ($id) => EntryController::export('instruction', $id));

$router->get('/plantillas', [EntryController::class, 'indexTemplates']);
$router->get('/plantillas/nuevo', fn () => EntryController::create('template'));
$router->post('/plantillas', fn () => EntryController::store('template'));
$router->get('/plantillas/{id}', fn ($id) => EntryController::show('template', $id));
$router->get('/plantillas/{id}/editar', fn ($id) => EntryController::edit('template', $id));
$router->post('/plantillas/{id}', fn ($id) => EntryController::update('template', $id));
$router->get('/plantillas/{id}/nueva-version', fn ($id) => EntryController::newVersionForm('template', $id));
$router->post('/plantillas/{id}/nueva-version', fn ($id) => EntryController::storeVersion('template', $id));
$router->post('/plantillas/{id}/eliminar', fn ($id) => EntryController::destroy('template', $id));
$router->get('/plantillas/{id}/exportar', fn ($id) => EntryController::export('template', $id));

$router->get('/pruebas', [TestController::class, 'index']);
$router->get('/pruebas/nueva', [TestController::class, 'create']);
$router->post('/pruebas', [TestController::class, 'store']);
$router->get('/pruebas/{id}', [TestController::class, 'show']);
$router->get('/pruebas/{id}/editar', [TestController::class, 'edit']);
$router->post('/pruebas/{id}', [TestController::class, 'update']);
$router->post('/pruebas/{id}/eliminar', [TestController::class, 'destroy']);

$router->get('/evaluaciones', [EvaluationController::class, 'index']);
$router->post('/evaluaciones', [EvaluationController::class, 'store']);
$router->post('/evaluaciones/{id}/eliminar', [EvaluationController::class, 'destroy']);

$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
