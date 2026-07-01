<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\Out\Jwt;

use App\Application\Port\Out\JwtGeneratorInterface;
use App\Domain\Entity\User;
use Firebase\JWT\JWT;

class FirebaseJwtGenerator implements JwtGeneratorInterface
{
    public function __construct(
        private readonly string $secretKey,
    ) {
    }

    public function generateToken(User $user): string
    {
        $payload = [
            'iss' => 'banque-auth-service',
            'sub' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'iat' => time(),
            'exp' => time() + 3600, // Expiration dans 1 heure
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }
}
