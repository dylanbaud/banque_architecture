<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\Out\Persistence;

use App\Application\Port\Out\ClientRepositoryInterface;
use App\Domain\Entity\Client;

class PdoClientRepository implements ClientRepositoryInterface
{
    private \PDO $pdo;

    public function __construct(string $dbDsn, string $dbUser, string $dbPassword)
    {
        $this->pdo = new \PDO($dbDsn, $dbUser, $dbPassword, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]);

        $this->initTable();
    }

    private function initTable(): void
    {
        $sql = 'CREATE TABLE IF NOT EXISTS client (
            id VARCHAR(50) PRIMARY KEY,
            nom VARCHAR(100) NOT NULL,
            prenom VARCHAR(100) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;';

        $this->pdo->exec($sql);
    }

    public function findById(string $id): ?Client
    {
        $stmt = $this->pdo->prepare('SELECT * FROM client WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return new Client($row['id'], $row['nom'], $row['prenom'], $row['email']);
    }

    public function save(Client $client): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO client (id, nom, prenom, email)
            VALUES (:id, :nom, :prenom, :email)
            ON DUPLICATE KEY UPDATE
                nom = VALUES(nom),
                prenom = VALUES(prenom),
                email = VALUES(email)
        ');

        $stmt->execute([
            'id' => $client->getId(),
            'nom' => $client->getNom(),
            'prenom' => $client->getPrenom(),
            'email' => $client->getEmail(),
        ]);
    }
}
