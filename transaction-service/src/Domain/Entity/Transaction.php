<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Exception\MontantInvalideException;

class Transaction
{
    public const STATUT_PENDING = 'PENDING';
    public const STATUT_COMPLETED = 'COMPLETED';
    public const STATUT_FAILED = 'FAILED';

    private string $id;
    private string $compteSourceId;
    private string $compteDestinationId;
    private float $montant;
    private \DateTimeImmutable $dateCreation;
    private string $statut;
    private ?string $motifEchec;

    /**
     * @throws MontantInvalideException
     */
    public function __construct(string $id, string $compteSourceId, string $compteDestinationId, float $montant)
    {
        if ($montant <= 0) {
            throw new MontantInvalideException('Le montant du virement doit être positif.');
        }

        $this->id = $id;
        $this->compteSourceId = $compteSourceId;
        $this->compteDestinationId = $compteDestinationId;
        $this->montant = $montant;
        $this->dateCreation = new \DateTimeImmutable();
        $this->statut = self::STATUT_PENDING;
        $this->motifEchec = null;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCompteSourceId(): string
    {
        return $this->compteSourceId;
    }

    public function getCompteDestinationId(): string
    {
        return $this->compteDestinationId;
    }

    public function getMontant(): float
    {
        return $this->montant;
    }

    public function getDateCreation(): \DateTimeImmutable
    {
        return $this->dateCreation;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function getMotifEchec(): ?string
    {
        return $this->motifEchec;
    }

    public function marquerCommeCompletee(): void
    {
        $this->statut = self::STATUT_COMPLETED;
    }

    public function marquerCommeEchouee(string $motif): void
    {
        $this->statut = self::STATUT_FAILED;
        $this->motifEchec = $motif;
    }
}
