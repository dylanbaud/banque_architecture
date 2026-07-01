<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Entity\Transaction;
use App\Domain\Exception\MontantInvalideException;
use PHPUnit\Framework\TestCase;

class TransactionTest extends TestCase
{
    public function testCreerTransactionInitialiseBienLesProprietes(): void
    {
        $transaction = new Transaction('trx_1', 'compte_1', 'compte_2', 100.0);

        $this->assertSame('trx_1', $transaction->getId());
        $this->assertSame('compte_1', $transaction->getCompteSourceId());
        $this->assertSame('compte_2', $transaction->getCompteDestinationId());
        $this->assertSame(100.0, $transaction->getMontant());
        $this->assertSame(Transaction::STATUT_PENDING, $transaction->getStatut());
        $this->assertNull($transaction->getMotifEchec());
    }

    public function testMontantNegatifLeveException(): void
    {
        $this->expectException(MontantInvalideException::class);
        new Transaction('trx_1', 'compte_1', 'compte_2', -50.0);
    }
}
