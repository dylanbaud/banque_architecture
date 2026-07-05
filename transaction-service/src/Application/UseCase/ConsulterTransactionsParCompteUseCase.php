<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Port\Out\TransactionRepositoryInterface;

class ConsulterTransactionsParCompteUseCase
{
    public function __construct(
        private readonly TransactionRepositoryInterface $transactionRepository,
    ) {
    }

    /** @return \App\Domain\Entity\Transaction[] */
    public function execute(string $compteId): array
    {
        return $this->transactionRepository->findByCompteId($compteId);
    }
}
