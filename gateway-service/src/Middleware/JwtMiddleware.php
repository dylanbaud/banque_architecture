<?php

declare(strict_types=1);

namespace Gateway\Middleware;

use Gateway\Auth\JwtDecoder;

readonly class JwtMiddleware
{
    private JwtDecoder $decoder;

    /**
     * @param array $config
     * @param array $publicPaths
     */
    public function __construct(
        array         $config,
        private array $publicPaths = [],
    ) {
        $this->decoder = new JwtDecoder(
            secret: $config['secret'],
            algorithm: $config['algorithm'],
            leeway: (int) ($config['leeway'] ?? 0),
        );
    }

    /**
     * @param array $request
     * @param callable $next
     * @return void
     */
    public function handle(array $request, callable $next): void
    {
        if ($this->isPublic($request['uri'])) {
            $next($request);
            return;
        }

        $token = $this->extractToken($request['headers']);

        if ($token === null) {
            $this->unauthorized('Missing Authorization header');
            return;
        }

        try {
            $payload = $this->decoder->decode($token);
        } catch (\RuntimeException $e) {
            $this->unauthorized($e->getMessage());
            return;
        }

        $request['headers']['X-User-Id'] = (string) ($payload['sub'] ?? '');
        $request['headers']['X-User-Roles'] = implode(',', (array) ($payload['roles'] ?? []));
        $request['headers']['X-User-Email'] = (string) ($payload['email'] ?? '');

        unset($request['headers']['Authorization']);
        $request['headers']['X-Auth-Verified'] = '1';

        $next($request);
    }

    /**
     * @param string $uri
     * @return bool
     */
    private function isPublic(string $uri): bool
    {
        foreach ($this->publicPaths as $path) {
            if (str_ends_with($path, '/*')) {
                $prefix = rtrim(substr($path, 0, -2), '/');
                if (str_starts_with(rtrim($uri, '/'), $prefix)) {
                    return true;
                }
            } elseif (rtrim($path, '/') === rtrim($uri, '/')) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param array $headers
     * @return string|null
     */
    private function extractToken(array $headers): ?string
    {
        foreach ($headers as $name => $value) {
            if (strtolower($name) === 'authorization') {
                if (str_starts_with($value, 'Bearer ')) {
                    return substr($value, 7);
                }
            }
        }
        return null;
    }

    /**
     * @param string $message
     * @return void
     */
    private function unauthorized(string $message): void
    {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => 'Unauthorized',
            'message' => $message,
        ]);
    }
}
