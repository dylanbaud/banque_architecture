<?php

namespace App\Application\UseCase;

use App\Application\Port\Out\CompteRepositoryInterface;
use App\Domain\Entity\Compte;
use App\Domain\Exception\CompteInexistantException;

class ConsulterCompteUseCase
{
    private CompteRepositoryInterface $compteRepository;

    public function __construct(CompteRepositoryInterface $compteRepository)
    {
        $this->compteRepository = $compteRepository;
    }

    /**
     * @throws CompteInexistantException
     */
    public function execute(string $id): Compte
    {
        $compte = $this->compteRepository->findById($id);

        if (null === $compte) {
            throw new CompteInexistantException($id);
        }

        return $compte;
    }
}
