<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Força o Laravel a entender que o script principal está na raiz, 
// para que ele não remova o prefixo "/api" da URL e encontre as rotas corretamente.
$_SERVER['SCRIPT_NAME'] = '/index.php';

require __DIR__.'/../../turetta_core/vendor/autoload.php';

$app = require_once __DIR__.'/../../turetta_core/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
$request = Request::capture();
file_put_contents(__DIR__.'/debug.log', "Path: " . $request->path() . "\nMethod: " . $request->method() . "\nURL: " . $request->url());
$response = $kernel->handle($request);

$response->send();

$kernel->terminate($request, $response);
