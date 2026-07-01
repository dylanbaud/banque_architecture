<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Port\Out\TransactionRepositoryInterface;
use App\Domain\Entity\Transaction;
use App\Domain\Exception\TransactionInexistanteException;

class ConsulterTransactionUseCase
{
    public function __construct(
        private readonly TransactionRepositoryInterface $transactionRepository,
    ) {
    }

    /**
     * @throws TransactionInexistanteException
     */
    public function execute(string $id): Transaction
    {
        $transaction = $this->transactionRepository->findById($id);

        if (null === $transaction) {
            throw new TransactionInexistanteException($id);
        }

        return $transaction;
    }
}
