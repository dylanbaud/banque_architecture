<?php

namespace App\Infrastructure\DTO;

use App\Domain\Entity\Compte;

class CompteResponseDTO
{
    public string $id;
    public string $clientId;
    public float $solde;
    public bool $estBloque;

    public static function fromEntity(Compte $compte): self
    {
        $dto = new self();
        $dto->id = $compte->getId();
        $dto->clientId = $compte->getClientId();
        $dto->solde = $compte->getSolde();
        $dto->estBloque = $compte->estBloque();

        return $dto;
    }
}
