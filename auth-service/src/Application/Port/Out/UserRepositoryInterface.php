<?php

declare(strict_types=1);

namespace App\Application\Port\Out;

use App\Domain\Entity\User;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?User;

    public function save(User $user): void;
}
