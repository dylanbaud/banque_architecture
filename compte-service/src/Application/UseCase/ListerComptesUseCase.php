<?php

namespace App\Application\UseCase;

use App\Application\Port\Out\CompteRepositoryInterface;

class ListerComptesUseCase
{
    public function __construct(
        private readonly CompteRepositoryInterface $compteRepository,
    ) {
    }

    /** @return \App\Domain\Entity\Compte[] */
    public function execute(): array
    {
        return $this->compteRepository->findAll();
    }
}
