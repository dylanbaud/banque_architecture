<?php

namespace App\Application\UseCase;

use App\Application\Port\Out\CompteRepositoryInterface;
use App\Domain\Exception\CompteBloqueException;
use App\Domain\Exception\CompteInexistantException;
use App\Domain\Exception\MontantInvalideException;

class DeposerArgentUseCase
{
    private CompteRepositoryInterface $compteRepository;

    public function __construct(CompteRepositoryInterface $compteRepository)
    {
        $this->compteRepository = $compteRepository;
    }

    /**
     * @throws CompteInexistantException
     * @throws MontantInvalideException
     * @throws CompteBloqueException
     */
    public function execute(string $id, float $montant): void
    {
        $compte = $this->compteRepository->findById($id);

        if (null === $compte) {
            throw new CompteInexistantException($id);
        }

        $compte->deposer($montant);

        $this->compteRepository->save($compte);
    }
}
