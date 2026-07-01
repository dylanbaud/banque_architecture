<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class User
{
    private string $id;
    private string $email;
    private string $passwordHash;
    /** @var array<string> */
    private array $roles;

    /**
     * @param array<string> $roles
     */
    public function __construct(string $id, string $email, string $passwordHash, array $roles = ['ROLE_USER'])
    {
        $this->id = $id;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->roles = $roles;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    /**
     * @return array<string>
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function verifyPassword(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->passwordHash);
    }
}
