<?php

namespace App\Tests\Application;

use App\Application\Port\Out\CompteRepositoryInterface;
use App\Application\UseCase\RetirerArgentUseCase;
use App\Domain\Entity\Compte;
use App\Domain\Exception\CompteBloqueException;
use App\Domain\Exception\CompteInexistantException;
use App\Domain\Exception\MontantInvalideException;
use App\Domain\Exception\SoldeInsuffisantException;
use PHPUnit\Framework\TestCase;

class RetirerArgentUseCaseTest extends TestCase
{
    /**
     * @throws CompteInexistantException
     * @throws MontantInvalideException
     * @throws SoldeInsuffisantException
     * @throws CompteBloqueException
     */
    public function testRetirerArgentSauvegardeLeCompteAvecSoldeDiminue(): void
    {
        // Arrange
        $compte = new Compte('cpt_1', 'client_1', 200.0);

        $repositoryMock = $this->createMock(CompteRepositoryInterface::class);
        $repositoryMock->method('findById')->willReturn($compte);
        $repositoryMock->expects($this->once())
            ->method('save')
            ->with($this->callback(fn (Compte $c) => 150.0 === $c->getSolde()));

        $useCase = new RetirerArgentUseCase($repositoryMock);

        // Act
        $useCase->execute('cpt_1', 50.0);

        // Assert — vérifié dans le callback du mock
    }

    /**
     * @throws MontantInvalideException
     * @throws SoldeInsuffisantException
     * @throws CompteBloqueException
     */
    public function testRetirerArgentCompteInexistantLeveException(): void
    {
        // Arrange
        $repositoryMock = $this->createStub(CompteRepositoryInterface::class);
        $repositoryMock->method('findById')->willReturn(null);

        $useCase = new RetirerArgentUseCase($repositoryMock);

        // Assert
        $this->expectException(CompteInexistantException::class);

        // Act
        $useCase->execute('cpt_inconnu', 50.0);
    }

    /**
     * @throws CompteInexistantException
     * @throws MontantInvalideException
     * @throws CompteBloqueException
     */
    public function testRetirerArgentSoldeInsuffisantLeveException(): void
    {
        // Arrange
        $compte = new Compte('cpt_1', 'client_1', 30.0);

        $repositoryMock = $this->createStub(CompteRepositoryInterface::class);
        $repositoryMock->method('findById')->willReturn($compte);

        $useCase = new RetirerArgentUseCase($repositoryMock);

        // Assert
        $this->expectException(SoldeInsuffisantException::class);

        // Act
        $useCase->execute('cpt_1', 100.0);
    }

    /**
     * @throws CompteInexistantException
     * @throws MontantInvalideException
     * @throws SoldeInsuffisantException
     */
    public function testRetirerArgentCompteBlockeLeveException(): void
    {
        // Arrange
        $compte = new Compte('cpt_1', 'client_1', 200.0);
        $compte->bloquer();

        $repositoryMock = $this->createStub(CompteRepositoryInterface::class);
        $repositoryMock->method('findById')->willReturn($compte);

        $useCase = new RetirerArgentUseCase($repositoryMock);

        // Assert
        $this->expectException(CompteBloqueException::class);

        // Act
        $useCase->execute('cpt_1', 50.0);
    }
}
