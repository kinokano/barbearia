<?php
/**
 * Barbearia Turetta — Bootstrap
 * 
 * Inicialização da aplicação:
 * - Autoload de classes
 * - Carregamento de variáveis de ambiente
 * - Configuração de error handler
 * - Timezone
 */

// ── Definições Globais ──────────────────────────
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('STORAGE_PATH', ROOT_PATH . '/storage');

// ── Timezone ────────────────────────────────────
date_default_timezone_set('America/Sao_Paulo');

// ── Error Handler ───────────────────────────────
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', STORAGE_PATH . '/logs/app.log');

// ── Autoloader Composer ─────────────────────────
$composerAutoload = ROOT_PATH . '/vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
}

// ── Variáveis de Ambiente (.env) ────────────────
$envFile = ROOT_PATH . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value, " \t\n\r\0\x0B\"'");
        $_ENV[$key] = $value;
        putenv("$key=$value");
    }
}

/**
 * Helper para acessar variáveis de ambiente
 */
function env(string $key, $default = null) {
    return $_ENV[$key] ?? getenv($key) ?: $default;
}

// ── Autoloader de Classes ───────────────────────
spl_autoload_register(function ($class) {
    // Converte namespace separado por \ em path de diretório
    $file = APP_PATH . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// ── Carregar Helpers ────────────────────────────
require_once APP_PATH . '/Helpers/Response.php';
require_once APP_PATH . '/Helpers/DateHelper.php';
require_once APP_PATH . '/Helpers/Sanitizer.php';

// ── Conexão com Banco de Dados ──────────────────
require_once ROOT_PATH . '/database/Connection.php';

// ── Configurações ───────────────────────────────
$GLOBALS['config'] = [
    'app'      => require CONFIG_PATH . '/app.php',
    'database' => require CONFIG_PATH . '/database.php',
    'auth'     => require CONFIG_PATH . '/auth.php',
    'cors'     => require CONFIG_PATH . '/cors.php',
    'mail'     => require CONFIG_PATH . '/mail.php',
    'tenant'   => require CONFIG_PATH . '/tenant.php',
];

/**
 * Helper para acessar configuração
 */
function config(string $key, $default = null) {
    $keys = explode('.', $key);
    $value = $GLOBALS['config'];
    foreach ($keys as $k) {
        if (!isset($value[$k])) return $default;
        $value = $value[$k];
    }
    return $value;
}

// ── CORS ────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}
