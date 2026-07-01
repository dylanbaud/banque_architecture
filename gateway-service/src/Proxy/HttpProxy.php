<?php

declare(strict_types=1);

namespace Gateway\Proxy;

class HttpProxy
{
    private const TIMEOUT         = 30;
    private const CONNECT_TIMEOUT = 5;

    // Headers that should never be forwarded upstream
    private const HOP_BY_HOP = [
        'connection', 'keep-alive', 'proxy-authenticate', 'proxy-authorization',
        'te', 'trailers', 'transfer-encoding', 'upgrade', 'host',
    ];

    /**
     * @param array $request
     * @return void
     */
    public function forward(array $request): void
    {
        $url = $request['target_url'];

        // Append query string
        if (!empty($request['query'])) {
            $url .= '?' . http_build_query($request['query']);
        }

        $curlHeaders = $this->buildCurlHeaders($request['headers']);

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_CUSTOMREQUEST  => $request['method'],
            CURLOPT_HTTPHEADER     => $curlHeaders,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => true,         // include response headers
            CURLOPT_TIMEOUT        => self::TIMEOUT,
            CURLOPT_CONNECTTIMEOUT => self::CONNECT_TIMEOUT,
            CURLOPT_FOLLOWLOCATION => false,
        ]);

        // Forward body for non-GET requests
        if (!in_array($request['method'], ['GET', 'HEAD'], true) && !empty($request['body'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request['body']);
        }

        $raw      = curl_exec($ch);
        $errno    = curl_errno($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);

        if ($errno !== CURLE_OK || $raw === false) {
            $this->gatewayError($url, curl_strerror($errno));
            return;
        }

        $responseHeaders = substr($raw, 0, $headerSize);
        $responseBody    = substr($raw, $headerSize);

        // Relay status
        http_response_code($httpCode);

        // Relay headers (skip hop-by-hop)
        foreach (explode("\r\n", $responseHeaders) as $line) {
            if (!str_contains($line, ':')) {
                continue;
            }
            [$name, $value] = explode(':', $line, 2);
            $nameLower = strtolower(trim($name));
            if (in_array($nameLower, self::HOP_BY_HOP, true)) {
                continue;
            }
            header(trim($name) . ':' . $value, false);
        }

        echo $responseBody;
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /**
     * @param array $headers
     * @return array
     */
    private function buildCurlHeaders(array $headers): array
    {
        $result = [];
        foreach ($headers as $name => $value) {
            if (in_array(strtolower($name), self::HOP_BY_HOP, true)) {
                continue;
            }
            $result[] = $name . ': ' . $value;
        }
        return $result;
    }

    /**
     * @param string $url
     * @param string $reason
     * @return void
     */
    private function gatewayError(string $url, string $reason): void
    {
        http_response_code(502);
        header('Content-Type: application/json');
        echo json_encode([
            'error'   => 'Bad Gateway',
            'message' => 'Upstream service unavailable',
            'detail'  => $reason,
            'url'     => $url,
        ]);
    }
}
