<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\Port\Out\TransactionRepositoryInterface;
use App\Application\UseCase\ConsulterTransactionUseCase;
use App\Domain\Entity\Transaction;
use App\Domain\Exception\TransactionInexistanteException;
use PHPUnit\Framework\TestCase;

class ConsulterTransactionUseCaseTest extends TestCase
{
    /**
     * @throws TransactionInexistanteException
     */
    public function testConsulterTransactionRetourneLaBonneTransaction(): void
    {
        // Arrange
        $transaction = new Transaction('trx_1', 'cpt_a', 'cpt_b', 100.0);

        $repositoryMock = $this->createMock(TransactionRepositoryInterface::class);
        $repositoryMock->expects($this->once())->method('findById')->with('trx_1')->willReturn($transaction);

        $useCase = new ConsulterTransactionUseCase($repositoryMock);

        // Act
        $result = $useCase->execute('trx_1');

        // Assert
        $this->assertSame('trx_1', $result->getId());
        $this->assertSame(100.0, $result->getMontant());
    }

    public function testConsulterTransactionInexistanteLeveException(): void
    {
        // Arrange
        $repositoryMock = $this->createStub(TransactionRepositoryInterface::class);
        $repositoryMock->method('findById')->willReturn(null);

        $useCase = new ConsulterTransactionUseCase($repositoryMock);

        // Assert
        $this->expectException(TransactionInexistanteException::class);

        // Act
        $useCase->execute('trx_inconnu');
    }
}
