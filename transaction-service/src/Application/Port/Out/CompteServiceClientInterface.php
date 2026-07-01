<?php

declare(strict_types=1);

namespace App\Application\Port\Out;

use App\Domain\Exception\CompteServiceException;

interface CompteServiceClientInterface
{
    /**
     * Effectue un retrait sur le compte source.
     *
     * @throws CompteServiceException En cas d'échec (solde insuffisant, compte bloqué, etc.)
     */
    public function retirer(string $compteId, float $montant): void;

    /**
     * Effectue un dépôt sur le compte destination.
     *
     * @throws CompteServiceException En cas d'échec (compte inexistant, etc.)
     */
    public function deposer(string $compteId, float $montant): void;
}
