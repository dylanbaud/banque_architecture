<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Port\Out\ClientRepositoryInterface;
use App\Domain\Entity\Client;

class CreerClientUseCase
{
    private ClientRepositoryInterface $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    public function execute(string $nom, string $prenom, string $email): Client
    {
        $id = uniqid('client_');
        $client = new Client($id, $nom, $prenom, $email);

        $this->clientRepository->save($client);

        return $client;
    }
}
