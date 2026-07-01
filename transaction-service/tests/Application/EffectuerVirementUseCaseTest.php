<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\Port\Out\CompteServiceClientInterface;
use App\Application\Port\Out\TransactionRepositoryInterface;
use App\Application\UseCase\EffectuerVirementUseCase;
use App\Domain\Entity\Transaction;
use App\Domain\Exception\CompteServiceException;
use PHPUnit\Framework\TestCase;

class EffectuerVirementUseCaseTest extends TestCase
{
    public function testVirementReussi(): void
    {
        $repoMock = $this->createMock(TransactionRepositoryInterface::class);
        $repoMock->expects($this->exactly(2))->method('save');

        $clientMock = $this->createMock(CompteServiceClientInterface::class);
        $clientMock->expects($this->once())->method('retirer')->with('c1', 100.0);
        $clientMock->expects($this->once())->method('deposer')->with('c2', 100.0);

        $useCase = new EffectuerVirementUseCase($repoMock, $clientMock);
        $transaction = $useCase->execute('c1', 'c2', 100.0);

        $this->assertSame(Transaction::STATUT_COMPLETED, $transaction->getStatut());
    }

    public function testVirementEchoueLorsDuRetrait(): void
    {
        $repoMock = $this->createMock(TransactionRepositoryInterface::class);
        $repoMock->expects($this->exactly(2))->method('save');

        $clientMock = $this->createMock(CompteServiceClientInterface::class);
        $clientMock->expects($this->once())->method('retirer')->with('c1', 100.0)->willThrowException(new CompteServiceException('Solde insuffisant'));
        $clientMock->expects($this->never())->method('deposer');

        $useCase = new EffectuerVirementUseCase($repoMock, $clientMock);

        $this->expectException(CompteServiceException::class);
        $useCase->execute('c1', 'c2', 100.0);
    }
}
