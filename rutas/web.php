<?php
// rutas/web.php
use Auth\Auth;

$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri    = preg_replace('#^/api#', '', $uri);
$method = $_SERVER['REQUEST_METHOD'];

// Primero lectura (gerenteRoutes), luego escritura (adminRoutes)
$routes = array_merge(
    require __DIR__ . '/gerenteRoutes.php',
    require __DIR__ . '/adminRoutes.php'
);

foreach ($routes as $route) {
    if ($route['path'] === $uri && in_array($method, $route['methods'], true)) {
        // middleware correcto
        call_user_func($route['middleware']);
        require_once __DIR__ . '/../' . $route['handler'];
        exit;
    }
}

// No coincide ninguna ruta
http_response_code(404);
header('Content-Type: application/json');
echo json_encode([ 'error' => 'Ruta no encontrada: ' . $uri ]);
