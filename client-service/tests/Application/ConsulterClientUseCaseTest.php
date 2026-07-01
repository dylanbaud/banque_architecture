<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\Port\Out\ClientRepositoryInterface;
use App\Application\UseCase\ConsulterClientUseCase;
use App\Domain\Entity\Client;
use App\Domain\Exception\ClientInexistantException;
use PHPUnit\Framework\TestCase;

class ConsulterClientUseCaseTest extends TestCase
{
    public function testConsulterClientExistantRetourneLeClient(): void
    {
        $client = new Client('client_1', 'Doe', 'John', 'john@doe.com');
        $repositoryStub = $this->createStub(ClientRepositoryInterface::class);
        $repositoryStub->method('findById')->willReturn($client);

        $useCase = new ConsulterClientUseCase($repositoryStub);
        $result = $useCase->execute('client_1');

        $this->assertSame($client, $result);
    }

    public function testConsulterClientInexistantLeveException(): void
    {
        $repositoryStub = $this->createStub(ClientRepositoryInterface::class);
        $repositoryStub->method('findById')->willReturn(null);

        $useCase = new ConsulterClientUseCase($repositoryStub);

        $this->expectException(ClientInexistantException::class);
        $useCase->execute('client_inexistant');
    }
}
