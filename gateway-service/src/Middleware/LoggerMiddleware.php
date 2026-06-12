<?php

declare(strict_types=1);

namespace Gateway\Middleware;

class LoggerMiddleware
{
    public function __construct(private readonly string $logFile) {}

    /** @param array<string, mixed> $request */
    public function handle(array $request, callable $next): void
    {
        $requestId = bin2hex(random_bytes(8));
        $startTime = microtime(true);

        header('X-Request-Id: ' . $requestId);

        $this->log($requestId, 'REQUEST', [
            'method' => $request['method'],
            'uri'    => $request['uri'],
            'ip'     => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'ua'     => $request['headers']['User-Agent'] ?? '',
        ]);

        // Capture response code after next runs
        ob_start();
        $next($request);
        $body = ob_get_clean();

        $duration = round((microtime(true) - $startTime) * 1000, 2);

        $this->log($requestId, 'RESPONSE', [
            'status'      => http_response_code(),
            'duration_ms' => $duration,
        ]);

        echo $body;
    }

    /** @param array<string, mixed> $context */
    private function log(string $requestId, string $event, array $context): void
    {
        $line = sprintf(
            "[%s] [%s] [%s] %s\n",
            date('Y-m-d H:i:s'),
            $requestId,
            $event,
            json_encode($context),
        );

        $dir = dirname($this->logFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($this->logFile, $line, FILE_APPEND | LOCK_EX);
    }
}