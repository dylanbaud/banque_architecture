<?php

namespace App\Infrastructure\Adapter\Out\Persistence;

use App\Application\Port\Out\CompteRepositoryInterface;
use App\Domain\Entity\Compte;

class InMemoryCompteRepository implements CompteRepositoryInterface
{
    /** @var array<string, Compte> */
    private array $comptes = [];

    public function save(Compte $compte): void
    {
        $this->comptes[$compte->getId()] = $compte;
    }

    public function findById(string $id): ?Compte
    {
        return $this->comptes[$id] ?? null;
    }
}
