<?php

namespace App\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

class CompteApiIntegrationTest extends KernelTestCase
{
    private const ACCOUNTS_PATH = '/accounts';
    private const ACCOUNTS_ID_PATH = '/accounts/';
    private string $compteId = '';

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->compteId = '';
    }

    private function request(string $method, string $uri, array $body = []): array
    {
        $kernel = self::bootKernel();

        $server = ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'];
        $content = $body ? json_encode($body) : null;

        $request  = Request::create($uri, $method, [], [], [], $server, $content);
        $response = $kernel->handle($request);

        return [
            'status'  => $response->getStatusCode(),
            'data'    => json_decode($response->getContent(), true) ?? [],
        ];
    }

    public function testCreerCompteRetourne201(): void
    {
        // Arrange + Act
        $result = $this->request('POST', self::ACCOUNTS_PATH, [
            'clientId'     => 'client_integration_test',
            'soldeInitial' => 200.0,
        ]);

        // Assert
        $this->assertSame(201, $result['status']);
        $this->assertArrayHasKey('id', $result['data']);
        $this->assertStringStartsWith('cpt_', $result['data']['id']);
        $this->assertEquals(200.0, $result['data']['solde']);

        $this->compteId = $result['data']['id'];
    }

    public function testConsulterCompteExistantRetourne200(): void
    {
        // Arrange — créer un compte d'abord
        $created = $this->request('POST', self::ACCOUNTS_PATH, [
            'clientId'     => 'client_integration_test',
            'soldeInitial' => 100.0,
        ]);
        $id = $created['data']['id'];

        // Act
        $result = $this->request('GET', self::ACCOUNTS_ID_PATH . $id);

        // Assert
        $this->assertSame(200, $result['status']);
        $this->assertSame($id, $result['data']['id']);
        $this->assertEquals(100.0, $result['data']['solde']);
    }

    public function testConsulterCompteInexistantRetourne404(): void
    {
        // Act
        $result = $this->request('GET', self::ACCOUNTS_ID_PATH . 'cpt_inexistant_xyz');

        // Assert
        $this->assertSame(404, $result['status']);
        $this->assertArrayHasKey('error', $result['data']);
    }

    public function testDeposerArgentMiseAJourLeSolde(): void
    {
        // Arrange
        $created = $this->request('POST', self::ACCOUNTS_PATH, [
            'clientId'     => 'client_integration_test',
            'soldeInitial' => 100.0,
        ]);
        $id = $created['data']['id'];

        // Act
        $result = $this->request('POST', self::ACCOUNTS_ID_PATH . $id . '/deposit', ['montant' => 50.0]);

        // Assert
        $this->assertSame(200, $result['status']);
        $this->assertEquals(150.0, $result['data']['solde']);
    }

    public function testRetirerArgentMiseAJourLeSolde(): void
    {
        // Arrange
        $created = $this->request('POST', self::ACCOUNTS_PATH, [
            'clientId'     => 'client_integration_test',
            'soldeInitial' => 200.0,
        ]);
        $id = $created['data']['id'];

        // Act
        $result = $this->request('POST', self::ACCOUNTS_ID_PATH . $id . '/withdraw', ['montant' => 80.0]);

        // Assert
        $this->assertSame(200, $result['status']);
        $this->assertEquals(120.0, $result['data']['solde']);
    }

    public function testRetirerArgentSoldeInsuffisantRetourne400(): void
    {
        // Arrange
        $created = $this->request('POST', self::ACCOUNTS_PATH, [
            'clientId'     => 'client_integration_test',
            'soldeInitial' => 10.0,
        ]);
        $id = $created['data']['id'];

        // Act
        $result = $this->request('POST', self::ACCOUNTS_ID_PATH . $id . '/withdraw', ['montant' => 500.0]);

        // Assert
        $this->assertSame(400, $result['status']);
        $this->assertArrayHasKey('error', $result['data']);
    }

    public function testDeposerMontantInvalideRetourne400(): void
    {
        // Arrange
        $created = $this->request('POST', self::ACCOUNTS_PATH, [
            'clientId'     => 'client_integration_test',
            'soldeInitial' => 100.0,
        ]);
        $id = $created['data']['id'];

        // Act
        $result = $this->request('POST', self::ACCOUNTS_ID_PATH . $id . '/deposit', ['montant' => -10.0]);

        // Assert
        $this->assertSame(400, $result['status']);
    }
}
