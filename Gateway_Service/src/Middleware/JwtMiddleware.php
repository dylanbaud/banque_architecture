<?php

declare(strict_types=1);

namespace Gateway\Middleware;

use Gateway\Auth\JwtDecoder;

class JwtMiddleware
{
    private readonly JwtDecoder $decoder;

    /**
     * @param array<string, mixed> $config
     * @param string[]             $publicPaths
     */
    public function __construct(
        array $config,
        private readonly array $publicPaths = [],
    ) {
        $this->decoder = new JwtDecoder(
            secret:    $config['secret'],
            algorithm: $config['algorithm'],
            leeway:    (int) ($config['leeway'] ?? 0),
        );
    }

    /** @param array<string, mixed> $request */
    public function handle(array $request, callable $next): void
    {
        // Pass through public paths
        if ($this->isPublic($request['uri'])) {
            $next($request);
            return;
        }

        // Extract token
        $token = $this->extractToken($request['headers']);

        if ($token === null) {
            $this->unauthorized('Missing Authorization header');
            return;
        }

        // Decode & validate
        try {
            $payload = $this->decoder->decode($token);
        } catch (\RuntimeException $e) {
            $this->unauthorized($e->getMessage());
            return;
        }

        // Forward user identity to upstream via headers
        $request['headers']['X-User-Id']    = (string) ($payload['sub'] ?? '');
        $request['headers']['X-User-Roles']  = implode(',', (array) ($payload['roles'] ?? []));
        $request['headers']['X-User-Email']  = (string) ($payload['email'] ?? '');

        // Strip the raw JWT before forwarding (optional security measure)
        unset($request['headers']['Authorization']);
        $request['headers']['X-Auth-Verified'] = '1';

        $next($request);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function isPublic(string $uri): bool
    {
        foreach ($this->publicPaths as $path) {
            if (rtrim($path, '/') === rtrim($uri, '/')) {
                return true;
            }
        }
        return false;
    }

    /** @param array<string, string> $headers */
    private function extractToken(array $headers): ?string
    {
        // Case-insensitive header lookup
        foreach ($headers as $name => $value) {
            if (strtolower($name) === 'authorization') {
                if (str_starts_with($value, 'Bearer ')) {
                    return substr($value, 7);
                }
            }
        }
        return null;
    }

    private function unauthorized(string $message): void
    {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode([
            'error'   => 'Unauthorized',
            'message' => $message,
        ]);
    }
}