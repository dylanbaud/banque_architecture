<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Port\Out\ClientRepositoryInterface;
use App\Domain\Entity\Client;
use App\Domain\Exception\ClientInexistantException;

class ConsulterClientUseCase
{
    private ClientRepositoryInterface $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    /**
     * @throws ClientInexistantException
     */
    public function execute(string $id): Client
    {
        $client = $this->clientRepository->findById($id);

        if (null === $client) {
            throw new ClientInexistantException($id);
        }

        return $client;
    }
}
