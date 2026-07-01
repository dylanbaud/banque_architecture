<?php

namespace App\Domain\Exception;

class MontantInvalideException extends \Exception
{
    public function __construct(string $message = 'Le montant doit être supérieur à zéro.')
    {
        parent::__construct($message);
    }
}
