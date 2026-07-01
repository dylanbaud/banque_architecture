<?php

declare(strict_types=1);

namespace Gateway\Auth;

use InvalidArgumentException;
use RuntimeException;

/**
 * Minimal JWT decoder – HS256 / HS384 / HS512 only.
 * No external library required.
 */
class JwtDecoder
{
    private const ALGOS = [
        'HS256' => 'sha256',
        'HS384' => 'sha384',
        'HS512' => 'sha512',
    ];

    /**
     * @param string $secret
     * @param string $algorithm
     * @param int $leeway
     */
    public function __construct(
        private readonly string $secret,
        private readonly string $algorithm = 'HS256',
        private readonly int    $leeway = 0,
    ) {
        if (!isset(self::ALGOS[$this->algorithm])) {
            throw new InvalidArgumentException("Unsupported algorithm: {$this->algorithm}");
        }
    }

    /**
     * @param string $token
     * @return array
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function decode(string $token): array
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            throw new RuntimeException('Invalid JWT structure');
        }

        [$headerB64, $payloadB64, $signatureB64] = $parts;

        $expected = $this->sign($headerB64 . '.' . $payloadB64);

        if (!hash_equals($expected, $this->base64UrlDecode($signatureB64))) {
            throw new RuntimeException('Invalid JWT signature');
        }

        $header = json_decode($this->base64UrlDecode($headerB64), true);

        if (($header['alg'] ?? '') !== $this->algorithm) {
            throw new RuntimeException("JWT algorithm mismatch: expected {$this->algorithm}");
        }

        $payload = json_decode($this->base64UrlDecode($payloadB64), true);

        if (!is_array($payload)) {
            throw new RuntimeException('Invalid JWT payload');
        }

        $now = time();

        if (isset($payload['exp']) && $now > ($payload['exp'] + $this->leeway)) {
            throw new RuntimeException('JWT has expired');
        }

        if (isset($payload['nbf']) && $now < ($payload['nbf'] - $this->leeway)) {
            throw new RuntimeException('JWT not yet valid');
        }

        if (isset($payload['iat']) && $now < ($payload['iat'] - $this->leeway)) {
            throw new RuntimeException('JWT issued in the future');
        }

        return $payload;
    }

    /**
     * @param array $payload
     * @return string
     */
    public function encode(array $payload): string
    {
        $headerB64 = $this->base64UrlEncode(json_encode(['typ' => 'JWT', 'alg' => $this->algorithm]));
        $payloadB64 = $this->base64UrlEncode(json_encode($payload));
        $signature = $this->sign($headerB64 . '.' . $payloadB64);

        return $headerB64 . '.' . $payloadB64 . '.' . $this->base64UrlEncode($signature);
    }

    /**
     * @param string $data
     * @return string
     */
    private function sign(string $data): string
    {
        return hash_hmac(self::ALGOS[$this->algorithm], $data, $this->secret, true);
    }

    /**
     * @param string $data
     * @return string
     */
    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * @param string $data
     * @return string
     */
    private function base64UrlDecode(string $data): string
    {
        $padded = str_pad(strtr($data, '-_', '+/'), strlen($data) + (4 - strlen($data) % 4) % 4, '=');
        $decoded = base64_decode($padded, strict: true);

        if ($decoded === false) {
            throw new RuntimeException('Invalid base64url encoding');
        }

        return $decoded;
    }
}
