<?php

declare(strict_types=1);

namespace App\Application\Port\Out;

use App\Domain\Entity\Client;

interface ClientRepositoryInterface
{
    public function save(Client $client): void;

    public function findById(string $id): ?Client;

    /** @return Client[] */
    public function findAll(): array;
}
