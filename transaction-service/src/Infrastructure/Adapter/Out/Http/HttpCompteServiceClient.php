<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\Out\Http;

use App\Application\Port\Out\CompteServiceClientInterface;
use App\Domain\Exception\CompteServiceException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HttpCompteServiceClient implements CompteServiceClientInterface
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly string $compteServiceUrl,
    ) {
    }

    public function retirer(string $compteId, float $montant): void
    {
        try {
            $response = $this->client->request('POST', sprintf('%s/accounts/%s/withdraw', rtrim($this->compteServiceUrl, '/'), $compteId), [
                'json' => ['montant' => $montant],
            ]);

            // Déclenche une exception si le statut n'est pas 200/204
            $response->getContent();
        } catch (ClientExceptionInterface $e) {
            $responseContent = json_decode($e->getResponse()->getContent(false), true);
            $message = $responseContent['error'] ?? 'Erreur lors du retrait sur le compte.';
            throw new CompteServiceException($message);
        } catch (\Throwable $e) {
            throw new CompteServiceException('Le service Compte est indisponible.');
        }
    }

    public function deposer(string $compteId, float $montant): void
    {
        try {
            $response = $this->client->request('POST', sprintf('%s/accounts/%s/deposit', rtrim($this->compteServiceUrl, '/'), $compteId), [
                'json' => ['montant' => $montant],
            ]);

            $response->getContent();
        } catch (ClientExceptionInterface $e) {
            $responseContent = json_decode($e->getResponse()->getContent(false), true);
            $message = $responseContent['error'] ?? 'Erreur lors du dépôt sur le compte.';
            throw new CompteServiceException($message);
        } catch (\Throwable $e) {
            throw new CompteServiceException('Le service Compte est indisponible.');
        }
    }
}
