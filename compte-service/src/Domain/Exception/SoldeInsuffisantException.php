<?php

namespace App\Domain\Exception;

class SoldeInsuffisantException extends \Exception
{
    public function __construct(string $message = 'Solde insuffisant pour effectuer cette opération.')
    {
        parent::__construct($message);
    }
}
