<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\Out\Persistence;

use App\Application\Port\Out\UserRepositoryInterface;
use App\Domain\Entity\User;

class PdoUserRepository implements UserRepositoryInterface
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
            CREATE TABLE IF NOT EXISTS utilisateur (
                id VARCHAR(255) PRIMARY KEY,
                email VARCHAR(255) UNIQUE NOT NULL,
                password_hash VARCHAR(255) NOT NULL,
                roles JSON NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ';
        $this->pdo->exec($sql);
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM utilisateur WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        /** @var array<string> $roles */
        $roles = json_decode((string) $row['roles'], true);

        return new User(
            (string) $row['id'],
            (string) $row['email'],
            (string) $row['password_hash'],
            $roles
        );
    }

    public function save(User $user): void
    {
        $sql = '
            INSERT INTO utilisateur (id, email, password_hash, roles)
            VALUES (:id, :email, :password_hash, :roles)
            ON DUPLICATE KEY UPDATE
                email = VALUES(email),
                password_hash = VALUES(password_hash),
                roles = VALUES(roles)
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'password_hash' => $user->getPasswordHash(),
            'roles' => json_encode($user->getRoles()),
        ]);
    }
}
