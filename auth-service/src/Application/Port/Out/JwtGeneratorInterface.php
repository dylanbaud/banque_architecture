<?php

declare(strict_types=1);

namespace App\Application\Port\Out;

use App\Domain\Entity\User;

interface JwtGeneratorInterface
{
    public function generateToken(User $user): string;
}
