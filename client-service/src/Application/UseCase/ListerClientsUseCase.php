<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Port\Out\ClientRepositoryInterface;
use App\Domain\Entity\Client;

class ListerClientsUseCase
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
    ) {
    }

    /** @return Client[] */
    public function execute(): array
    {
        return $this->clientRepository->findAll();
    }
}
