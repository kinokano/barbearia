<?php
/**
 * Barbearia Turetta — API Router
 * 
 * Roteia as requests /api/* para os controllers adequados.
 */

// ── Parse da Request ────────────────────────────
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri    = $_SERVER['REQUEST_URI'];

// Remove query string e prefix /api
$path = parse_url($requestUri, PHP_URL_PATH);
$path = preg_replace('#^/api#', '', $path);
$path = rtrim($path, '/') ?: '/';

// ── Content-Type JSON ───────────────────────────
header('Content-Type: application/json; charset=utf-8');

// ── Carregar Subrotas ───────────────────────────
$routes = [];

// Rotas públicas (sem auth)
require_once __DIR__ . '/public.php';

// Rotas de autenticação
require_once __DIR__ . '/auth.php';

// Rotas admin (com auth + role check)
require_once __DIR__ . '/admin.php';

// ── Resolver Rota ───────────────────────────────
$matched = false;

foreach ($routes as $route) {
    if ($route['method'] !== $requestMethod) continue;

    // Transforma /profissionais/:id em regex
    $pattern = preg_replace('#:([a-zA-Z_]+)#', '(?P<$1>[^/]+)', $route['path']);
    $pattern = '#^' . $pattern . '$#';

    if (preg_match($pattern, $path, $matches)) {
        $matched = true;

        // Extrair parâmetros nomeados
        $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

        // Executar middlewares
        if (!empty($route['middleware'])) {
            foreach ($route['middleware'] as $middleware) {
                $middlewareFile = APP_PATH . '/Middleware/' . $middleware . '.php';
                if (file_exists($middlewareFile)) {
                    require_once $middlewareFile;
                    $middlewareClass = $middleware;
                    $result = $middlewareClass::handle();
                    if ($result !== true) {
                        echo json_encode($result);
                        exit;
                    }
                }
            }
        }

        // Executar controller
        $controllerFile = APP_PATH . '/Controllers/' . $route['controller'] . '.php';
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $controller = new $route['controller']();
            $method = $route['action'];

            // Ler body JSON
            $body = json_decode(file_get_contents('php://input'), true) ?? [];

            $controller->$method($params, $body);
        } else {
            Response::error('Controller não encontrado.', 500);
        }

        break;
    }
}

if (!$matched) {
    Response::error('Rota não encontrada.', 404);
}
