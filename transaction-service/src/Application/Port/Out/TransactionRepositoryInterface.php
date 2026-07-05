<?php

declare(strict_types=1);

namespace App\Application\Port\Out;

use App\Domain\Entity\Transaction;

interface TransactionRepositoryInterface
{
    public function findById(string $id): ?Transaction;

    public function save(Transaction $transaction): void;

    /** @return Transaction[] */
    public function findAll(): array;

    /** @return Transaction[] */
    public function findByCompteId(string $compteId): array;
}
