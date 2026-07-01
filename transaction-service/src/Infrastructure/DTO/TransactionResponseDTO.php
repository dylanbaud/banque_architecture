<?php

declare(strict_types=1);

namespace App\Infrastructure\DTO;

use App\Domain\Entity\Transaction;

class TransactionResponseDTO
{
    /**
     * @return array<string, mixed>
     */
    public static function fromEntity(Transaction $transaction): array
    {
        return [
            'id' => $transaction->getId(),
            'compteSourceId' => $transaction->getCompteSourceId(),
            'compteDestinationId' => $transaction->getCompteDestinationId(),
            'montant' => $transaction->getMontant(),
            'dateCreation' => $transaction->getDateCreation()->format(\DateTimeInterface::ATOM),
            'statut' => $transaction->getStatut(),
            'motifEchec' => $transaction->getMotifEchec(),
        ];
    }
}
