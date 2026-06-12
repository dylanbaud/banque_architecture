<?php

declare(strict_types=1);

namespace Gateway\Middleware;

class CorsMiddleware
{
    /** @param array<string, mixed> $config */
    public function __construct(private readonly array $config) {}

    /** @param array<string, mixed> $request */
    public function handle(array $request, callable $next): void
    {
        $origin = $request['headers']['Origin'] ?? $request['headers']['origin'] ?? '*';

        $allowedOrigins = $this->config['allowed_origins'];
        $originAllowed  = in_array('*', $allowedOrigins, true) || in_array($origin, $allowedOrigins, true);

        if ($originAllowed) {
            header('Access-Control-Allow-Origin: ' . (in_array('*', $allowedOrigins, true) ? '*' : $origin));
        }

        header('Access-Control-Allow-Methods: ' . implode(', ', $this->config['allowed_methods']));
        header('Access-Control-Allow-Headers: ' . implode(', ', $this->config['allowed_headers']));
        header('Access-Control-Expose-Headers: ' . implode(', ', $this->config['expose_headers']));
        header('Access-Control-Max-Age: ' . $this->config['max_age']);

        if ($this->config['allow_credentials']) {
            header('Access-Control-Allow-Credentials: true');
        }

        // Handle preflight
        if ($request['method'] === 'OPTIONS') {
            http_response_code(204);
            return;
        }

        $next($request);
    }
}