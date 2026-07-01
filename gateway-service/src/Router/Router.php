<?php

declare(strict_types=1);

namespace Gateway\Router;

class Router
{
    /**
     * @param array $routes
     */
    public function __construct(private readonly array $routes) {}

    /**
     * Match an incoming request against registered routes.
     *
     * @param string $method
     * @param string $uri
     * @return array|null
     */
    public function match(string $method, string $uri): ?array
    {
        $uri = rtrim($uri, '/') ?: '/';

        foreach ($this->routes as $route) {
            // Method check
            if ($route['methods'] !== ['*'] && !in_array($method, $route['methods'], true)) {
                continue;
            }

            // Convert pattern to regex  e.g. /users/{id} → #^/users/(?P<id>[^/]+)$#
            $pattern = $this->toRegex($route['pattern']);

            if (!preg_match($pattern, $uri, $matches)) {
                continue;
            }

            // Collect named captures
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

            // Build upstream path with param substitution
            $upstreamPath = $route['upstream_path'];
            foreach ($params as $name => $value) {
                $upstreamPath = str_replace('{' . $name . '}', urlencode($value), $upstreamPath);
            }

            return [
                'service'       => $route['service'],
                'upstream_path' => $upstreamPath,
                'params'        => $params,
                'pattern'       => $route['pattern'],
            ];
        }

        return null;
    }

    /**
     * @param string $pattern
     * @return string
     */
    private function toRegex(string $pattern): string
    {
        $pattern = rtrim($pattern, '/') ?: '/';
        $regex   = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $pattern);

        return '#^' . $regex . '$#';
    }
}
