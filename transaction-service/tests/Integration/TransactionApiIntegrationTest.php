<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

class TransactionApiIntegrationTest extends KernelTestCase
{
    private const TRANSACTIONS_PATH    = '/transactions';
    private const TRANSACTIONS_ID_PATH = '/transactions/';
    private const ACCOUNTS_TRX_PATH    = '/accounts/';

    private function request(string $method, string $uri, array $body = []): array
    {
        $kernel = self::bootKernel();

        $server  = ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'];
        $content = $body ? json_encode($body) : null;

        $request  = Request::create($uri, $method, [], [], [], $server, $content);
        $response = $kernel->handle($request);

        return [
            'status' => $response->getStatusCode(),
            'data'   => json_decode($response->getContent(), true) ?? [],
        ];
    }

    public function testListerTransactionsRetourne200AvecTableau(): void
    {
        // Act
        $result = $this->request('GET', self::TRANSACTIONS_PATH);

        // Assert
        $this->assertSame(200, $result['status']);
        $this->assertIsArray($result['data']);
    }

    public function testConsulterTransactionInexistanteRetourne404(): void
    {
        // Act
        $result = $this->request('GET', self::TRANSACTIONS_ID_PATH . 'trx_inconnu_xyz');

        // Assert
        $this->assertSame(404, $result['status']);
        $this->assertArrayHasKey('error', $result['data']);
    }

    public function testListerTransactionsParCompteRetourne200(): void
    {
        // Act
        $result = $this->request('GET', self::ACCOUNTS_TRX_PATH . 'cpt_inexistant_xyz/transactions');

        // Assert — retourne 200 avec tableau vide si aucune transaction
        $this->assertSame(200, $result['status']);
        $this->assertIsArray($result['data']);
    }

    public function testEffectuerVirementMontantInvalideRetourne400(): void
    {
        // Act
        $result = $this->request('POST', self::TRANSACTIONS_PATH, [
            'compteSourceId'      => 'cpt_a',
            'compteDestinationId' => 'cpt_b',
            'montant'             => -50.0,
        ]);

        // Assert
        $this->assertSame(400, $result['status']);
    }

    public function testEffectuerVirementDonneesManquantesRetourne400(): void
    {
        // Act — montant manquant
        $result = $this->request('POST', self::TRANSACTIONS_PATH, [
            'compteSourceId' => 'cpt_a',
        ]);

        // Assert
        $this->assertSame(400, $result['status']);
        $this->assertArrayHasKey('error', $result['data']);
    }
}
