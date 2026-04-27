<?php
/**
 * Rotas de Autenticação
 */

$routes[] = [
    'method'     => 'POST',
    'path'       => '/auth/login',
    'controller' => 'AuthController',
    'action'     => 'login',
    'middleware'  => [],
];

$routes[] = [
    'method'     => 'POST',
    'path'       => '/auth/register',
    'controller' => 'AuthController',
    'action'     => 'register',
    'middleware'  => [],
];

$routes[] = [
    'method'     => 'POST',
    'path'       => '/auth/refresh',
    'controller' => 'AuthController',
    'action'     => 'refresh',
    'middleware'  => [],
];

$routes[] = [
    'method'     => 'POST',
    'path'       => '/auth/logout',
    'controller' => 'AuthController',
    'action'     => 'logout',
    'middleware'  => ['AuthMiddleware'],
];

$routes[] = [
    'method'     => 'GET',
    'path'       => '/auth/me',
    'controller' => 'AuthController',
    'action'     => 'me',
    'middleware'  => ['AuthMiddleware'],
];
