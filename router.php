<?php

$uri = parse_url($_SERVER['REQUEST_URI'])['path'];

$routes = [
    '/' => 'controllers/login.php',
    '/aluno' => 'controllers/aluno.php',
    '/coordenador' => 'controllers/coordenador.php',
    '/professor' => 'controllers/professor.php',
    '/admin' => 'controllers/admin.php',
];

function routerToController($uri, $routes) {
    if (array_key_exists($uri, $routes)) {
        require $routes[$uri];
    } else {
        abort();
    }
}

function abort($code = 404) {
    http_response_code($code);
    require "views/{$code}.view.php";
    die;
}

routerToController($uri, $routes);