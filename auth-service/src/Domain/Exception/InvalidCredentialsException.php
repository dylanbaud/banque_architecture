<?php

declare(strict_types=1);

namespace App\Domain\Exception;

class InvalidCredentialsException extends \DomainException
{
    public function __construct()
    {
        parent::__construct('Identifiants invalides.');
    }
}
