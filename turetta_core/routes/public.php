<?php
/**
 * Rotas Públicas (sem autenticação)
 */

// Listar serviços ativos
$routes[] = [
    'method'     => 'GET',
    'path'       => '/servicos',
    'controller' => 'ServicoController',
    'action'     => 'index',
    'middleware'  => [],
];

// Listar profissionais ativos
$routes[] = [
    'method'     => 'GET',
    'path'       => '/profissionais',
    'controller' => 'ProfissionalController',
    'action'     => 'index',
    'middleware'  => [],
];

// Verificar disponibilidade de horários
$routes[] = [
    'method'     => 'GET',
    'path'       => '/disponibilidade/:profissional_id/:data',
    'controller' => 'AgendamentoController',
    'action'     => 'disponibilidade',
    'middleware'  => [],
];

// Criar agendamento (público, pode exigir login)
$routes[] = [
    'method'     => 'POST',
    'path'       => '/agendamentos',
    'controller' => 'AgendamentoController',
    'action'     => 'store',
    'middleware'  => ['AuthMiddleware'],
];

// Config pública do tenant (nome, logo, horários)
$routes[] = [
    'method'     => 'GET',
    'path'       => '/config',
    'controller' => 'ConfigController',
    'action'     => 'public',
    'middleware'  => [],
];
