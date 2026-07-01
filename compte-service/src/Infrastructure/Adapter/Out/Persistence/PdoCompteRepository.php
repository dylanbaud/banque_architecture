<?php

namespace App\Infrastructure\Adapter\Out\Persistence;

use App\Application\Port\Out\CompteRepositoryInterface;
use App\Domain\Entity\Compte;

class PdoCompteRepository implements CompteRepositoryInterface
{
    private \PDO $pdo;

    public function __construct(string $dbDsn, string $dbUser, string $dbPassword)
    {
        $this->pdo = new \PDO($dbDsn, $dbUser, $dbPassword, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]);

        $this->pdo->exec('
            CREATE TABLE IF NOT EXISTS comptes (
                id VARCHAR(255) PRIMARY KEY,
                client_id VARCHAR(255) NOT NULL,
                solde DECIMAL(10, 2) NOT NULL,
                est_bloque TINYINT(1) NOT NULL DEFAULT 0
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ');
    }

    public function findById(string $id): ?Compte
    {
        $stmt = $this->pdo->prepare('SELECT * FROM comptes WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        $compte = new Compte($row['id'], $row['client_id'], (float) $row['solde']);
        if ($row['est_bloque']) {
            $compte->bloquer();
        }

        return $compte;
    }

    public function save(Compte $compte): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO comptes (id, client_id, solde, est_bloque)
            VALUES (:id, :client_id, :solde, :est_bloque)
            ON DUPLICATE KEY UPDATE
                solde = VALUES(solde),
                est_bloque = VALUES(est_bloque)
        ');

        $stmt->execute([
            'id' => $compte->getId(),
            'client_id' => $compte->getClientId(),
            'solde' => $compte->getSolde(),
            'est_bloque' => $compte->estBloque() ? 1 : 0,
        ]);
    }
}
