<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Port\Out\TransactionRepositoryInterface;

class ListerTransactionsUseCase
{
    public function __construct(
        private readonly TransactionRepositoryInterface $transactionRepository,
    ) {
    }

    /** @return \App\Domain\Entity\Transaction[] */
    public function execute(): array
    {
        return $this->transactionRepository->findAll();
    }
}
