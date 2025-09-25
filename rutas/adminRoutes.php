<?php
// rutas/adminRoutes.php
use Auth\Middleware;

return [
    // Crear/editar/eliminar usuarios (POST)
    [
        'path'       => '/usuario',
        'methods'    => ['POST','PUT','DELETE'],
        'handler'    => 'controller/usuario.php',
        'middleware' => function() { Middleware::checkAuth('admin'); }
    ],
    // Crear/editar/eliminar categorías
    [
        'path'       => '/categoria',
        'methods'    => ['POST','PUT','DELETE'],
        'handler'    => 'controller/categoria.php',
        'middleware' => function() { Middleware::checkAuth('admin'); }
    ],
    // Crear/editar/eliminar instructores
    [
        'path'       => '/instructor',
        'methods'    => ['POST','PUT','DELETE'],
        'handler'    => 'controller/instructor.php',
        'middleware' => function() { Middleware::checkAuth('admin'); }
    ],
    // Crear/editar/eliminar cursos
    [
        'path'       => '/curso',
        'methods'    => ['POST','PUT','DELETE'],
        'handler'    => 'controller/curso.php',
        'middleware' => function() { Middleware::checkAuth('admin'); }
    ],
];
