<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\Out\Persistence;

use App\Application\Port\Out\TransactionRepositoryInterface;
use App\Domain\Entity\Transaction;

class PdoTransactionRepository implements TransactionRepositoryInterface
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
        $sql = '
            CREATE TABLE IF NOT EXISTS transaction (
                id VARCHAR(255) PRIMARY KEY,
                compte_source_id VARCHAR(255) NOT NULL,
                compte_destination_id VARCHAR(255) NOT NULL,
                montant DECIMAL(15,2) NOT NULL,
                date_creation DATETIME NOT NULL,
                statut VARCHAR(50) NOT NULL,
                motif_echec TEXT DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ';
        $this->pdo->exec($sql);
    }

    public function findById(string $id): ?Transaction
    {
        $stmt = $this->pdo->prepare('SELECT * FROM transaction WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return Transaction::reconstruct(
            $row['id'],
            $row['compte_source_id'],
            $row['compte_destination_id'],
            (float) $row['montant'],
            new \DateTimeImmutable($row['date_creation']),
            $row['statut'],
            $row['motif_echec'],
        );
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM transaction ORDER BY date_creation DESC');
        $rows = $stmt->fetchAll();

        return array_map(fn (array $row) => Transaction::reconstruct(
            $row['id'],
            $row['compte_source_id'],
            $row['compte_destination_id'],
            (float) $row['montant'],
            new \DateTimeImmutable($row['date_creation']),
            $row['statut'],
            $row['motif_echec'],
        ), $rows);
    }

    public function save(Transaction $transaction): void
    {
        $sql = '
            INSERT INTO transaction (id, compte_source_id, compte_destination_id, montant, date_creation, statut, motif_echec)
            VALUES (:id, :compte_source_id, :compte_destination_id, :montant, :date_creation, :statut, :motif_echec)
            ON DUPLICATE KEY UPDATE
                statut = VALUES(statut),
                motif_echec = VALUES(motif_echec)
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $transaction->getId(),
            'compte_source_id' => $transaction->getCompteSourceId(),
            'compte_destination_id' => $transaction->getCompteDestinationId(),
            'montant' => $transaction->getMontant(),
            'date_creation' => $transaction->getDateCreation()->format('Y-m-d H:i:s'),
            'statut' => $transaction->getStatut(),
            'motif_echec' => $transaction->getMotifEchec(),
        ]);
    }
}
