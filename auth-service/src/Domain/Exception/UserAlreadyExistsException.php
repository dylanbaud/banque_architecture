<?php

declare(strict_types=1);

namespace App\Domain\Exception;

class UserAlreadyExistsException extends \DomainException
{
    public function __construct(string $email)
    {
        parent::__construct(sprintf('Un utilisateur avec l\'email "%s" existe déjà.', $email));
    }
}
