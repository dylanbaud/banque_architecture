<?php

declare(strict_types=1);

return [

    // ── Services (microservices base URLs) ──────────────────────────────────
    'services' => [
        'users'    => 'http://users-service:8001',
        'orders'   => 'http://orders-service:8002',
        'products' => 'http://products-service:8003',
        'auth'        => 'http://auth-nginx:80',
        'compte'      => 'http://compte-nginx:80',
        'client'      => 'http://client-nginx:80',
        'transaction' => 'http://transaction-nginx:80',
    ],

    // ── Routes ─────────────────────────────────────────────────────────────
    // pattern       : URL pattern (supports {param} placeholders)
    // service       : key from 'services' above
    // upstream_path : path forwarded to the service ({param} replaced)
    // methods       : allowed HTTP methods (* = all)
    'routes' => [
        // Auth service (public)
        ['pattern' => '/auth/login',    'service' => 'auth',     'upstream_path' => '/login',           'methods' => ['POST']],
        ['pattern' => '/auth/register', 'service' => 'auth',     'upstream_path' => '/register',        'methods' => ['POST']],
        ['pattern' => '/auth/refresh',  'service' => 'auth',     'upstream_path' => '/refresh',         'methods' => ['POST']],

        // Users service (protected)
        ['pattern' => '/users',           'service' => 'users',  'upstream_path' => '/users',           'methods' => ['GET', 'POST']],
        ['pattern' => '/users/{id}',      'service' => 'users',  'upstream_path' => '/users/{id}',      'methods' => ['GET', 'PUT', 'DELETE']],
        ['pattern' => '/users/{id}/profile', 'service' => 'users', 'upstream_path' => '/users/{id}/profile', 'methods' => ['GET', 'PUT']],

        // Products service (protected)
        ['pattern' => '/products',        'service' => 'products', 'upstream_path' => '/products',      'methods' => ['GET', 'POST']],
        ['pattern' => '/products/{id}',   'service' => 'products', 'upstream_path' => '/products/{id}', 'methods' => ['GET', 'PUT', 'DELETE']],

        // Orders service (protected)
        ['pattern' => '/orders',          'service' => 'orders', 'upstream_path' => '/orders',          'methods' => ['GET', 'POST']],
        ['pattern' => '/orders/{id}',     'service' => 'orders', 'upstream_path' => '/orders/{id}',     'methods' => ['GET', 'PUT']],

        // Compte service
        ['pattern' => '/accounts',                 'service' => 'compte', 'upstream_path' => '/accounts',                 'methods' => ['GET', 'POST']],
        ['pattern' => '/accounts/{id}',            'service' => 'compte', 'upstream_path' => '/accounts/{id}',            'methods' => ['GET']],
        ['pattern' => '/accounts/{id}/deposit',    'service' => 'compte', 'upstream_path' => '/accounts/{id}/deposit',    'methods' => ['POST']],
        ['pattern' => '/accounts/{id}/withdraw',   'service' => 'compte', 'upstream_path' => '/accounts/{id}/withdraw',   'methods' => ['POST']],

        // Client service
        ['pattern' => '/clients',                  'service' => 'client', 'upstream_path' => '/clients',                  'methods' => ['GET', 'POST']],
        ['pattern' => '/clients/{id}',             'service' => 'client', 'upstream_path' => '/clients/{id}',             'methods' => ['GET']],

        // Transaction service
        ['pattern' => '/transactions',             'service' => 'transaction', 'upstream_path' => '/transactions',             'methods' => ['GET', 'POST']],
        ['pattern' => '/transactions/{id}',        'service' => 'transaction', 'upstream_path' => '/transactions/{id}',        'methods' => ['GET']],
    ],

    // ── Public paths (no JWT required) ─────────────────────────────────────
    'public_paths' => [
        '/auth/login',
        '/auth/register',
        '/auth/refresh',
    ],

    // ── JWT ─────────────────────────────────────────────────────────────────
    'jwt' => [
        'secret'    => getenv('JWT_SECRET') ?? 'change-me-in-production',
        'algorithm' => 'HS256',
        'leeway'    => 10,          // seconds of clock skew tolerated
    ],

    // ── CORS ────────────────────────────────────────────────────────────────
    'cors' => [
        'allowed_origins'  => ['*'],
        'allowed_methods'  => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
        'allowed_headers'  => ['Content-Type', 'Authorization', 'X-Requested-With'],
        'expose_headers'   => ['X-Request-Id'],
        'max_age'          => 3600,
        'allow_credentials' => false,
    ],

    // ── Logging ─────────────────────────────────────────────────────────────
    'log_file' => '/tmp/gateway.log',

];
