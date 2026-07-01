<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Port\Out\CompteServiceClientInterface;
use App\Application\Port\Out\TransactionRepositoryInterface;
use App\Domain\Entity\Transaction;
use App\Domain\Exception\CompteServiceException;

class EffectuerVirementUseCase
{
    public function __construct(
        private readonly TransactionRepositoryInterface $transactionRepository,
        private readonly CompteServiceClientInterface $compteServiceClient,
    ) {
    }

    public function execute(string $compteSourceId, string $compteDestinationId, float $montant): Transaction
    {
        $id = 'trx_'.uniqid();
        $transaction = new Transaction($id, $compteSourceId, $compteDestinationId, $montant);

        // Enregistrer la transaction en statut PENDING
        $this->transactionRepository->save($transaction);

        try {
            // 1. Retirer de la source
            $this->compteServiceClient->retirer($compteSourceId, $montant);

            // 2. Déposer sur la destination
            $this->compteServiceClient->deposer($compteDestinationId, $montant);

            // 3. Marquer comme complétée
            $transaction->marquerCommeCompletee();
        } catch (CompteServiceException $e) {
            // En cas d'erreur sur l'un des comptes, la transaction échoue.
            // (Idéalement, il faudrait gérer la compensation "Saga" si le retrait a réussi
            // mais que le dépôt a échoué. Pour simplifier, on suppose que l'erreur indique un échec direct).
            $transaction->marquerCommeEchouee($e->getMessage());
            $this->transactionRepository->save($transaction);
            throw $e;
        }

        $this->transactionRepository->save($transaction);

        return $transaction;
    }
}
