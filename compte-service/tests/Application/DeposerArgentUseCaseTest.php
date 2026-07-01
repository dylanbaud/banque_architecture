<?php

namespace App\Tests\Application;

use App\Application\Port\Out\CompteRepositoryInterface;
use App\Application\UseCase\DeposerArgentUseCase;
use App\Domain\Entity\Compte;
use App\Domain\Exception\CompteBloqueException;
use App\Domain\Exception\CompteInexistantException;
use App\Domain\Exception\MontantInvalideException;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class DeposerArgentUseCaseTest extends TestCase
{
    /**
     * @throws Exception
     * @throws MontantInvalideException
     * @throws CompteBloqueException
     */
    public function testDeposerArgentCompteInexistantLeveException(): void
    {
        $repositoryMock = $this->createStub(CompteRepositoryInterface::class);
        $repositoryMock->method('findById')->willReturn(null);

        $useCase = new DeposerArgentUseCase($repositoryMock);

        $this->expectException(CompteInexistantException::class);

        $useCase->execute('cpt_inconnu', 50.0);
    }

    /**
     * @throws CompteInexistantException
     * @throws Exception
     * @throws MontantInvalideException
     * @throws CompteBloqueException
     */
    public function testDeposerArgentSauvegardeLeCompte(): void
    {
        $compte = new Compte('cpt_1', 'client_1', 100.0);

        $repositoryMock = $this->createMock(CompteRepositoryInterface::class);
        $repositoryMock->method('findById')->willReturn($compte);

        $repositoryMock->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Compte $c) {
                return 150.0 === $c->getSolde();
            }));

        $useCase = new DeposerArgentUseCase($repositoryMock);

        $useCase->execute('cpt_1', 50.0);
    }
}
