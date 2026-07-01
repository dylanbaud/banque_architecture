<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Port\Out\UserRepositoryInterface;
use App\Domain\Entity\User;
use App\Domain\Exception\UserAlreadyExistsException;

class RegisterUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * @throws UserAlreadyExistsException
     */
    public function execute(string $email, string $plainPassword): User
    {
        $existingUser = $this->userRepository->findByEmail($email);
        if (null !== $existingUser) {
            throw new UserAlreadyExistsException($email);
        }

        $id = 'usr_'.uniqid();
        $passwordHash = password_hash($plainPassword, PASSWORD_BCRYPT);

        $user = new User($id, $email, $passwordHash);
        $this->userRepository->save($user);

        return $user;
    }
}
