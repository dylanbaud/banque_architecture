<?php

namespace App\Domain\Entity;

use App\Domain\Exception\CompteBloqueException;
use App\Domain\Exception\MontantInvalideException;
use App\Domain\Exception\SoldeInsuffisantException;

class Compte
{
    private string $id;
    private string $clientId;
    private float $solde;
    private bool $estBloque;

    /**
     * @throws MontantInvalideException
     */
    public function __construct(string $id, string $clientId, float $soldeInitial = 0.0)
    {
        if ($soldeInitial < 0) {
            throw new MontantInvalideException('Le solde initial ne peut pas être négatif.');
        }
        $this->id = $id;
        $this->clientId = $clientId;
        $this->solde = $soldeInitial;
        $this->estBloque = false;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getSolde(): float
    {
        return $this->solde;
    }

    public function estBloque(): bool
    {
        return $this->estBloque;
    }

    public function bloquer(): void
    {
        $this->estBloque = true;
    }

    public function debloquer(): void
    {
        $this->estBloque = false;
    }

    /**
     * @throws MontantInvalideException
     * @throws CompteBloqueException
     */
    public function deposer(float $montant): void
    {
        if ($this->estBloque) {
            throw new CompteBloqueException();
        }
        if ($montant <= 0) {
            throw new MontantInvalideException();
        }
        $this->solde += $montant;
    }

    /**
     * @throws MontantInvalideException
     * @throws CompteBloqueException
     * @throws SoldeInsuffisantException
     */
    public function retirer(float $montant): void
    {
        if ($this->estBloque) {
            throw new CompteBloqueException();
        }
        if ($montant <= 0) {
            throw new MontantInvalideException();
        }
        if ($this->solde < $montant) {
            throw new SoldeInsuffisantException();
        }
        $this->solde -= $montant;
    }
}
