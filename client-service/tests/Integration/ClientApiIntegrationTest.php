<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

class ClientApiIntegrationTest extends KernelTestCase
{
    private const CLIENTS_PATH    = '/clients';
    private const CLIENTS_ID_PATH = '/clients/';

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

    public function testCreerClientRetourne201(): void
    {
        // Arrange + Act
        $result = $this->request('POST', self::CLIENTS_PATH, [
            'nom'    => 'Dupont',
            'prenom' => 'Jean',
            'email'  => 'jean.dupont.' . uniqid() . '@test.fr',
        ]);

        // Assert
        $this->assertSame(201, $result['status']);
        $this->assertArrayHasKey('id', $result['data']);
        $this->assertStringStartsWith('client_', $result['data']['id']);
        $this->assertSame('Dupont', $result['data']['nom']);
    }

    public function testConsulterClientExistantRetourne200(): void
    {
        // Arrange
        $created = $this->request('POST', self::CLIENTS_PATH, [
            'nom'    => 'Martin',
            'prenom' => 'Alice',
            'email'  => 'alice.martin.' . uniqid() . '@test.fr',
        ]);
        $id = $created['data']['id'];

        // Act
        $result = $this->request('GET', self::CLIENTS_ID_PATH . $id);

        // Assert
        $this->assertSame(200, $result['status']);
        $this->assertSame($id, $result['data']['id']);
        $this->assertSame('Martin', $result['data']['nom']);
        $this->assertSame('Alice', $result['data']['prenom']);
    }

    public function testConsulterClientInexistantRetourne404(): void
    {
        // Act
        $result = $this->request('GET', self::CLIENTS_ID_PATH . 'client_inconnu_xyz');

        // Assert
        $this->assertSame(404, $result['status']);
        $this->assertArrayHasKey('error', $result['data']);
    }

    public function testListerClientsRetourne200AvecTableau(): void
    {
        // Act
        $result = $this->request('GET', self::CLIENTS_PATH);

        // Assert
        $this->assertSame(200, $result['status']);
        $this->assertIsArray($result['data']);
    }

    public function testCreerClientDonneesManquantesRetourne400(): void
    {
        // Act — email manquant
        $result = $this->request('POST', self::CLIENTS_PATH, [
            'nom'    => 'Test',
            'prenom' => 'Test',
        ]);

        // Assert
        $this->assertSame(400, $result['status']);
    }
}
