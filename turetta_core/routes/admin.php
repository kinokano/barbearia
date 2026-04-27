<?php
/**
 * Rotas Admin (requerem autenticação + role admin)
 */

$adminMiddleware = ['AuthMiddleware', 'AdminMiddleware'];

// ── Dashboard ───────────────────────────────────
$routes[] = [
    'method'     => 'GET',
    'path'       => '/admin/dashboard',
    'controller' => 'DashboardController',
    'action'     => 'index',
    'middleware'  => $adminMiddleware,
];

// ── Agendamentos ────────────────────────────────
$routes[] = [
    'method'     => 'GET',
    'path'       => '/admin/agendamentos',
    'controller' => 'AgendamentoController',
    'action'     => 'adminIndex',
    'middleware'  => $adminMiddleware,
];

$routes[] = [
    'method'     => 'PUT',
    'path'       => '/admin/agendamentos/:id',
    'controller' => 'AgendamentoController',
    'action'     => 'update',
    'middleware'  => $adminMiddleware,
];

$routes[] = [
    'method'     => 'DELETE',
    'path'       => '/admin/agendamentos/:id',
    'controller' => 'AgendamentoController',
    'action'     => 'destroy',
    'middleware'  => $adminMiddleware,
];

// ── Clientes ────────────────────────────────────
$routes[] = [
    'method'     => 'GET',
    'path'       => '/admin/clientes',
    'controller' => 'ClienteController',
    'action'     => 'index',
    'middleware'  => $adminMiddleware,
];

$routes[] = [
    'method'     => 'GET',
    'path'       => '/admin/clientes/:id',
    'controller' => 'ClienteController',
    'action'     => 'show',
    'middleware'  => $adminMiddleware,
];

$routes[] = [
    'method'     => 'POST',
    'path'       => '/admin/clientes',
    'controller' => 'ClienteController',
    'action'     => 'store',
    'middleware'  => $adminMiddleware,
];

$routes[] = [
    'method'     => 'PUT',
    'path'       => '/admin/clientes/:id',
    'controller' => 'ClienteController',
    'action'     => 'update',
    'middleware'  => $adminMiddleware,
];

// ── Profissionais ───────────────────────────────
$routes[] = [
    'method'     => 'POST',
    'path'       => '/admin/profissionais',
    'controller' => 'ProfissionalController',
    'action'     => 'store',
    'middleware'  => $adminMiddleware,
];

$routes[] = [
    'method'     => 'PUT',
    'path'       => '/admin/profissionais/:id',
    'controller' => 'ProfissionalController',
    'action'     => 'update',
    'middleware'  => $adminMiddleware,
];

$routes[] = [
    'method'     => 'DELETE',
    'path'       => '/admin/profissionais/:id',
    'controller' => 'ProfissionalController',
    'action'     => 'destroy',
    'middleware'  => $adminMiddleware,
];

// ── Serviços ────────────────────────────────────
$routes[] = [
    'method'     => 'POST',
    'path'       => '/admin/servicos',
    'controller' => 'ServicoController',
    'action'     => 'store',
    'middleware'  => $adminMiddleware,
];

$routes[] = [
    'method'     => 'PUT',
    'path'       => '/admin/servicos/:id',
    'controller' => 'ServicoController',
    'action'     => 'update',
    'middleware'  => $adminMiddleware,
];

$routes[] = [
    'method'     => 'DELETE',
    'path'       => '/admin/servicos/:id',
    'controller' => 'ServicoController',
    'action'     => 'destroy',
    'middleware'  => $adminMiddleware,
];

// ── Relatórios ──────────────────────────────────
$routes[] = [
    'method'     => 'GET',
    'path'       => '/admin/relatorios',
    'controller' => 'RelatorioController',
    'action'     => 'index',
    'middleware'  => $adminMiddleware,
];

// ── Configurações ───────────────────────────────
$routes[] = [
    'method'     => 'GET',
    'path'       => '/admin/config',
    'controller' => 'ConfigController',
    'action'     => 'index',
    'middleware'  => $adminMiddleware,
];

$routes[] = [
    'method'     => 'PUT',
    'path'       => '/admin/config',
    'controller' => 'ConfigController',
    'action'     => 'update',
    'middleware'  => $adminMiddleware,
];
