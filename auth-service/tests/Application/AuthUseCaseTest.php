<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\Port\Out\JwtGeneratorInterface;
use App\Application\Port\Out\UserRepositoryInterface;
use App\Application\UseCase\LoginUseCase;
use App\Application\UseCase\RegisterUseCase;
use App\Domain\Entity\User;
use App\Domain\Exception\InvalidCredentialsException;
use App\Domain\Exception\UserAlreadyExistsException;
use PHPUnit\Framework\TestCase;

class AuthUseCaseTest extends TestCase
{
    public function testRegisterCreeBienUnUtilisateur(): void
    {
        $repoMock = $this->createMock(UserRepositoryInterface::class);
        $repoMock->expects($this->once())->method('findByEmail')->with('test@test.com')->willReturn(null);
        $repoMock->expects($this->once())->method('save');

        $useCase = new RegisterUseCase($repoMock);
        $user = $useCase->execute('test@test.com', 'password123');

        $this->assertSame('test@test.com', $user->getEmail());
        $this->assertTrue($user->verifyPassword('password123'));
    }

    public function testRegisterEchoueSiEmailExistant(): void
    {
        $existingUser = new User('1', 'test@test.com', 'hash');

        $repoMock = $this->createMock(UserRepositoryInterface::class);
        $repoMock->expects($this->once())->method('findByEmail')->with('test@test.com')->willReturn($existingUser);
        $repoMock->expects($this->never())->method('save');

        $useCase = new RegisterUseCase($repoMock);

        $this->expectException(UserAlreadyExistsException::class);
        $useCase->execute('test@test.com', 'password123');
    }

    public function testLoginReussiRetourneToken(): void
    {
        $hash = password_hash('password123', PASSWORD_BCRYPT);
        $user = new User('1', 'test@test.com', $hash);

        $repoMock = $this->createMock(UserRepositoryInterface::class);
        $repoMock->expects($this->once())->method('findByEmail')->with('test@test.com')->willReturn($user);

        $jwtMock = $this->createMock(JwtGeneratorInterface::class);
        $jwtMock->expects($this->once())->method('generateToken')->with($user)->willReturn('fake.jwt.token');

        $useCase = new LoginUseCase($repoMock, $jwtMock);
        $token = $useCase->execute('test@test.com', 'password123');

        $this->assertSame('fake.jwt.token', $token);
    }

    public function testLoginEchoueSiMauvaisMotDePasse(): void
    {
        $hash = password_hash('password123', PASSWORD_BCRYPT);
        $user = new User('1', 'test@test.com', $hash);

        $repoMock = $this->createMock(UserRepositoryInterface::class);
        $repoMock->expects($this->once())->method('findByEmail')->with('test@test.com')->willReturn($user);

        $jwtMock = $this->createMock(JwtGeneratorInterface::class);

        $useCase = new LoginUseCase($repoMock, $jwtMock);

        $this->expectException(InvalidCredentialsException::class);
        $useCase->execute('test@test.com', 'wrong_password');
    }
}
