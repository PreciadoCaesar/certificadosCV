<?php
// rutas/gerenteRoutes.php
use Auth\Middleware;

return [
    // Listar usuarios activos (ambos roles)
    [
        'path'       => '/usuario',
        'methods'    => ['GET', 'POST'],
        'handler'    => 'controller/usuario.php',
        'middleware' => function() { Middleware::checkAny(['admin','gerente']); }
    ],
    // Listar categorías
    [
        'path'       => '/categoria',
        'methods'    => ['GET', 'POST'],
        'handler'    => 'controller/categoria.php',
        'middleware' => function() { Middleware::checkAny(['admin','gerente']); }
    ],
    // Listar instructores
    [
        'path'       => '/instructor',
        'methods'    => ['GET', 'POST'],
        'handler'    => 'controller/instructor.php',
        'middleware' => function() { Middleware::checkAny(['admin','gerente']); }
    ],
    // Listar cursos
    [
        'path'       => '/curso',
        'methods'    => ['GET', 'POST'],
        'handler'    => 'controller/curso.php',
        'middleware' => function() { Middleware::checkAny(['admin','gerente']); }
    ],
];
