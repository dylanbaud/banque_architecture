<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Entity\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function testCreerClientInitialiseBienLesProprietes(): void
    {
        $client = new Client('client_1', 'Doe', 'John', 'john@doe.com');

        $this->assertSame('client_1', $client->getId());
        $this->assertSame('Doe', $client->getNom());
        $this->assertSame('John', $client->getPrenom());
        $this->assertSame('john@doe.com', $client->getEmail());
    }
}
