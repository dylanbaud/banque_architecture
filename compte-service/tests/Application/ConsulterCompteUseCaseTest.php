<?php

namespace App\Tests\Application;

use App\Application\Port\Out\CompteRepositoryInterface;
use App\Application\UseCase\ConsulterCompteUseCase;
use App\Domain\Entity\Compte;
use App\Domain\Exception\CompteInexistantException;
use PHPUnit\Framework\TestCase;

class ConsulterCompteUseCaseTest extends TestCase
{
    /**
     * @throws CompteInexistantException
     */
    public function testConsulterCompteRetourneLeBonCompte(): void
    {
        // Arrange
        $compte = new Compte('cpt_1', 'client_1', 300.0);

        $repositoryMock = $this->createMock(CompteRepositoryInterface::class);
        $repositoryMock->expects($this->once())->method('findById')->with('cpt_1')->willReturn($compte);

        $useCase = new ConsulterCompteUseCase($repositoryMock);

        // Act
        $result = $useCase->execute('cpt_1');

        // Assert
        $this->assertSame('cpt_1', $result->getId());
        $this->assertSame(300.0, $result->getSolde());
    }

    public function testConsulterCompteInexistantLeveException(): void
    {
        // Arrange
        $repositoryMock = $this->createStub(CompteRepositoryInterface::class);
        $repositoryMock->method('findById')->willReturn(null);

        $useCase = new ConsulterCompteUseCase($repositoryMock);

        // Assert
        $this->expectException(CompteInexistantException::class);

        // Act
        $useCase->execute('cpt_inconnu');
    }
}
