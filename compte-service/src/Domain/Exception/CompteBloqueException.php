<?php

namespace App\Domain\Exception;

class CompteBloqueException extends \Exception
{
    public function __construct(string $message = 'Opération impossible, le compte est bloqué.')
    {
        parent::__construct($message);
    }
}
