<?php

namespace App\Domain\Exception;

class CompteInexistantException extends \Exception
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf("Le compte '%s' n'existe pas.", $id));
    }
}
