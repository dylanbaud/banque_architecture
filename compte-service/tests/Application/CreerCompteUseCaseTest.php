<?php

namespace App\Tests\Application;

use App\Application\Port\Out\CompteRepositoryInterface;
use App\Application\UseCase\CreerCompteUseCase;
use App\Domain\Exception\MontantInvalideException;
use PHPUnit\Framework\TestCase;

class CreerCompteUseCaseTest extends TestCase
{
    /**
     * @throws MontantInvalideException
     */
    public function testCreerCompteRetourneLeCompteEtLeSauvegarde(): void
    {
        // Arrange
        $repositoryMock = $this->createMock(CompteRepositoryInterface::class);
        $repositoryMock->expects($this->once())->method('save');

        $useCase = new CreerCompteUseCase($repositoryMock);

        // Act
        $compte = $useCase->execute('client_1', 500.0);

        // Assert
        $this->assertStringStartsWith('cpt_', $compte->getId());
        $this->assertSame('client_1', $compte->getClientId());
        $this->assertSame(500.0, $compte->getSolde());
        $this->assertFalse($compte->estBloque());
    }

    /**
     * @throws MontantInvalideException
     */
    public function testCreerCompteAvecSoldeParDefautEstZero(): void
    {
        // Arrange
        $repositoryMock = $this->createMock(CompteRepositoryInterface::class);
        $repositoryMock->expects($this->once())->method('save');

        $useCase = new CreerCompteUseCase($repositoryMock);

        // Act
        $compte = $useCase->execute('client_1');

        // Assert
        $this->assertSame(0.0, $compte->getSolde());
    }

    public function testCreerCompteAvecSoldeNegatifLeveException(): void
    {
        // Arrange
        $repositoryMock = $this->createStub(CompteRepositoryInterface::class);
        $useCase = new CreerCompteUseCase($repositoryMock);

        // Assert
        $this->expectException(MontantInvalideException::class);

        // Act
        $useCase->execute('client_1', -100.0);
    }
}
