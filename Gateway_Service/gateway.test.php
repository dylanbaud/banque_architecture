<?php

declare(strict_types=1);

namespace Gateway\Tests;

use Gateway\Auth\JwtDecoder;
use Gateway\Router\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    private Router $router;

    protected function setUp(): void
    {
        $this->router = new Router([
            ['pattern' => '/auth/login',  'service' => 'auth',    'upstream_path' => '/login',       'methods' => ['POST']],
            ['pattern' => '/users',       'service' => 'users',   'upstream_path' => '/users',       'methods' => ['GET', 'POST']],
            ['pattern' => '/users/{id}',  'service' => 'users',   'upstream_path' => '/users/{id}',  'methods' => ['GET', 'PUT', 'DELETE']],
        ]);
    }

    public function testMatchesExactRoute(): void
    {
        $result = $this->router->match('POST', '/auth/login');
        $this->assertNotNull($result);
        $this->assertSame('auth', $result['service']);
        $this->assertSame('/login', $result['upstream_path']);
    }

    public function testMatchesRouteWithParam(): void
    {
        $result = $this->router->match('GET', '/users/42');
        $this->assertNotNull($result);
        $this->assertSame('users', $result['service']);
        $this->assertSame('/users/42', $result['upstream_path']);
        $this->assertSame('42', $result['params']['id']);
    }

    public function testReturnsNullForUnknownRoute(): void
    {
        $result = $this->router->match('GET', '/nonexistent');
        $this->assertNull($result);
    }

    public function testRejectsWrongMethod(): void
    {
        $result = $this->router->match('DELETE', '/auth/login');
        $this->assertNull($result);
    }
}

class JwtDecoderTest extends TestCase
{
    private JwtDecoder $decoder;
    private string $secret = 'test-secret-key';

    protected function setUp(): void
    {
        $this->decoder = new JwtDecoder($this->secret, 'HS256', leeway: 5);
    }

    public function testEncodeAndDecode(): void
    {
        $payload = ['sub' => '123', 'email' => 'user@example.com', 'exp' => time() + 3600];
        $token   = $this->decoder->encode($payload);
        $decoded = $this->decoder->decode($token);

        $this->assertSame('123', $decoded['sub']);
        $this->assertSame('user@example.com', $decoded['email']);
    }

    public function testThrowsOnExpiredToken(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('expired');

        $payload = ['sub' => '1', 'exp' => time() - 100];
        $token   = $this->decoder->encode($payload);
        $this->decoder->decode($token);
    }

    public function testThrowsOnTamperedSignature(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('signature');

        $payload = ['sub' => '1', 'exp' => time() + 3600];
        $token   = $this->decoder->encode($payload);
        $tampered = $token . 'x';
        $this->decoder->decode($tampered);
    }

    public function testThrowsOnWrongSecret(): void
    {
        $this->expectException(\RuntimeException::class);

        $other   = new JwtDecoder('other-secret', 'HS256');
        $payload = ['sub' => '1', 'exp' => time() + 3600];
        $token   = $other->encode($payload);

        $this->decoder->decode($token);
    }
}