<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Application\Port\Out\ClientRepositoryInterface;
use App\Application\UseCase\CreerClientUseCase;
use PHPUnit\Framework\TestCase;

class CreerClientUseCaseTest extends TestCase
{
    public function testCreerClientRetourneLeClientEtLeSauvegarde(): void
    {
        $repositoryMock = $this->createMock(ClientRepositoryInterface::class);

        $repositoryMock->expects($this->once())
            ->method('save');

        $useCase = new CreerClientUseCase($repositoryMock);
        $client = $useCase->execute('Doe', 'John', 'john@doe.com');

        $this->assertSame('Doe', $client->getNom());
        $this->assertStringStartsWith('client_', $client->getId());
    }
}
