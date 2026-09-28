<?php
define('BASE_PATH', __DIR__);
require __DIR__ . '/core/Router.php';
$router = new Router;
require __DIR__ . '/routes.php';


$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

$file = $router->direct($uri);

if (!$file || !file_exists(__DIR__ . '/' . $file)) {
    http_response_code(404);
    exit('404 Not Found');
}

require __DIR__ . '/' . $file;