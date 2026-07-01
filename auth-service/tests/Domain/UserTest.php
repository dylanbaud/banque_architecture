<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testVerificationMotDePasse(): void
    {
        $hash = password_hash('monSuperMotDePasse', PASSWORD_BCRYPT);
        $user = new User('usr_1', 'test@example.com', $hash);

        $this->assertTrue($user->verifyPassword('monSuperMotDePasse'));
        $this->assertFalse($user->verifyPassword('mauvaisMotDePasse'));
    }

    public function testRolesParDefaut(): void
    {
        $hash = password_hash('monSuperMotDePasse', PASSWORD_BCRYPT);
        $user = new User('usr_1', 'test@example.com', $hash);

        $this->assertSame(['ROLE_USER'], $user->getRoles());
    }
}
