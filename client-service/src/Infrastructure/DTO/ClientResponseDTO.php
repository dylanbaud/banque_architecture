<?php

declare(strict_types=1);

namespace App\Infrastructure\DTO;

use App\Domain\Entity\Client;

class ClientResponseDTO
{
    public string $id;
    public string $nom;
    public string $prenom;
    public string $email;

    public static function fromEntity(Client $client): self
    {
        $dto = new self();
        $dto->id = $client->getId();
        $dto->nom = $client->getNom();
        $dto->prenom = $client->getPrenom();
        $dto->email = $client->getEmail();

        return $dto;
    }
}
