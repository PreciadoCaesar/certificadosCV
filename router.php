<?php
// index.php (front-controller)

session_start();

// 1) Carga de dependencias mínimas
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/auth/Auth.php';
require_once __DIR__ . '/auth/Middleware.php';

use Auth\Auth;
use Auth\Middleware;

// 2) Control de acceso global: si no hay usuario logueado
$user = Auth::user();
if (empty($user['id'])) {
    // 2.1) Si es AJAX (DataTables, Fetch, XHR), devolvemos JSON 401
    if (
        ! empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
    ) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'No autenticado']);
        exit;
    }
    // 2.2) Si es petición normal, redirigimos al login
    header('Location: ' . Conectar::ruta());
    exit;
}

// 3) Parseo de URI y método HTTP
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base   = parse_url(Conectar::ruta(), PHP_URL_PATH);
if ($base !== '/' && strpos($uri, $base) === 0) {
    $uri = substr($uri, strlen($base));
}
$uri = '/' . ltrim($uri, '/');
// Si usamos prefijo /api para nuestras rutas REST:
if (strpos($uri, '/api') === 0) {
    $uri = substr($uri, strlen('/api'));
}
$method = $_SERVER['REQUEST_METHOD'];

// 4) Carga de rutas (primero gerente, luego admin)
$routes = array_merge(
    require __DIR__ . '/rutas/gerenteRoutes.php',
    require __DIR__ . '/rutas/adminRoutes.php'
);

// 5) Bucle para encontrar ruta coincidente
foreach ($routes as $route) {
    if ($route['path'] === $uri && in_array($method, $route['methods'], true)) {
        // 5.1) Ejecutar middleware (rol)
        call_user_func($route['middleware']);
        // 5.2) Ejecutar handler
        require_once __DIR__ . '/' . $route['handler'];
        exit;
    }
}

// 6) Si no se encontró ruta, devolvemos 404 JSON
http_response_code(404);
header('Content-Type: application/json');
echo json_encode(['error' => 'Ruta no encontrada: ' . $uri]);
