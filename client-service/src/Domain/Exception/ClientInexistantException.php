<?php

declare(strict_types=1);

namespace App\Domain\Exception;

class ClientInexistantException extends \Exception
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf("Le client '%s' n'existe pas.", $id));
    }
}
