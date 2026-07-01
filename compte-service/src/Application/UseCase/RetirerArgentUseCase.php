<?php

namespace App\Application\UseCase;

use App\Application\Port\Out\CompteRepositoryInterface;
use App\Domain\Exception\CompteBloqueException;
use App\Domain\Exception\CompteInexistantException;
use App\Domain\Exception\MontantInvalideException;
use App\Domain\Exception\SoldeInsuffisantException;

class RetirerArgentUseCase
{
    private CompteRepositoryInterface $compteRepository;

    public function __construct(CompteRepositoryInterface $compteRepository)
    {
        $this->compteRepository = $compteRepository;
    }

    /**
     * @throws CompteInexistantException
     * @throws MontantInvalideException
     * @throws SoldeInsuffisantException
     * @throws CompteBloqueException
     */
    public function execute(string $id, float $montant): void
    {
        $compte = $this->compteRepository->findById($id);

        if (null === $compte) {
            throw new CompteInexistantException($id);
        }

        $compte->retirer($montant);

        $this->compteRepository->save($compte);
    }
}
