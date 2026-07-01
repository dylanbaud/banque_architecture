<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Port\Out\JwtGeneratorInterface;
use App\Application\Port\Out\UserRepositoryInterface;
use App\Domain\Exception\InvalidCredentialsException;

class LoginUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly JwtGeneratorInterface $jwtGenerator,
    ) {
    }

    /**
     * @return string Le token JWT
     *
     * @throws InvalidCredentialsException
     */
    public function execute(string $email, string $plainPassword): string
    {
        $user = $this->userRepository->findByEmail($email);

        if (null === $user || !$user->verifyPassword($plainPassword)) {
            throw new InvalidCredentialsException();
        }

        return $this->jwtGenerator->generateToken($user);
    }
}
