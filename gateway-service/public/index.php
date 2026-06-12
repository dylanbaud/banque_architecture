<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Gateway\Router\Router;
use Gateway\Middleware\JwtMiddleware;
use Gateway\Middleware\CorsMiddleware;
use Gateway\Middleware\LoggerMiddleware;
use Gateway\Proxy\HttpProxy;

$config = require __DIR__ . '/../config/gateway.php';

$router  = new Router($config['routes']);
$proxy   = new HttpProxy();

$middlewares = [
    new LoggerMiddleware($config['log_file']),
    new CorsMiddleware($config['cors']),
    new JwtMiddleware($config['jwt'], $config['public_paths']),
];

$request = [
    'method'  => $_SERVER['REQUEST_METHOD'],
    'uri'     => parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),
    'headers' => getallheaders(),
    'query'   => $_GET,
    'body'    => file_get_contents('php://input'),
];

if ($request['method'] === 'GET' && $request['uri'] === '/') {
    require __DIR__ . '/home.php';
    exit;
}

// Run middleware pipeline
$next = function (array $req) use ($router, $proxy, $config): void {
    $route = $router->match($req['method'], $req['uri']);

    if (!$route) {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Route not found', 'path' => $req['uri']]);
        return;
    }

    // Inject path params & resolved service URL
    $req['params']      = $route['params'];
    $req['target_url']  = $config['services'][$route['service']] . $route['upstream_path'];

    $proxy->forward($req);
};

// Wrap pipeline
$pipeline = array_reduce(
    array_reverse($middlewares),
    fn ($carry, $mw) => fn ($req) => $mw->handle($req, $carry),
    $next
);

($pipeline)($request);