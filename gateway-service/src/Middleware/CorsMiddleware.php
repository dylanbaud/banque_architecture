<?php

declare(strict_types=1);

namespace Gateway\Middleware;

readonly class CorsMiddleware
{
    /**
     * @param array $config
     */
    public function __construct(private array $config) {}

    /**
     * @param array $request
     * @param callable $next
     * @return void
     */
    public function handle(array $request, callable $next): void
    {
        $origin = $request['headers']['Origin'] ?? $request['headers']['origin'] ?? '*';

        $allowedOrigins = $this->config['allowed_origins'];
        $originAllowed = in_array('*', $allowedOrigins, true) || in_array($origin, $allowedOrigins, true);

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

        if ($request['method'] === 'OPTIONS') {
            http_response_code(204);
            return;
        }

        $next($request);
    }
}
