<?php

declare(strict_types=1);

namespace App\Domain\Exception;

class TransactionInexistanteException extends \DomainException
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf('La transaction avec l\'id "%s" n\'existe pas.', $id));
    }
}
