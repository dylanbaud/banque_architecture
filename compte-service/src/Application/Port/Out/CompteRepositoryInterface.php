<?php

namespace App\Application\Port\Out;

use App\Domain\Entity\Compte;

interface CompteRepositoryInterface
{
    public function save(Compte $compte): void;

    public function findById(string $id): ?Compte;
}
