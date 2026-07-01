<?php

namespace App\Application\UseCase;

use App\Application\Port\Out\CompteRepositoryInterface;
use App\Domain\Entity\Compte;
use App\Domain\Exception\MontantInvalideException;

class CreerCompteUseCase
{
    private CompteRepositoryInterface $compteRepository;

    public function __construct(CompteRepositoryInterface $compteRepository)
    {
        $this->compteRepository = $compteRepository;
    }

    /**
     * @throws MontantInvalideException
     */
    public function execute(string $clientId, float $soldeInitial = 0.0): Compte
    {
        $id = uniqid('cpt_');
        $compte = new Compte($id, $clientId, $soldeInitial);

        $this->compteRepository->save($compte);

        return $compte;
    }
}
