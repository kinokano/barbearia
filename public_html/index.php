<?php

/**
 * Barbearia Turetta — Entry Point para cPanel/Hostgator.
 *
 * Este arquivo fica em public_html/ e aponta para o core do Laravel
 * que está um nível acima, em turetta/.
 *
 * Em produção (Hostgator):
 *   require __DIR__.'/../turetta/vendor/autoload.php';
 *   $app = require_once __DIR__.'/../turetta/bootstrap/app.php';
 *
 * Em desenvolvimento local, este arquivo não é necessário —
 * use `php artisan serve` dentro da pasta turetta/.
 */

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Ajuste estes caminhos conforme seu servidor
require __DIR__.'/../turetta/vendor/autoload.php';

$app = require_once __DIR__.'/../turetta/bootstrap/app.php';

// Define o caminho público como public_html
$app->usePublicPath(__DIR__);

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
