<?php

namespace App\Exceptions;

use Exception;

class InvalidTokenException extends Exception
{
    public function __construct()
    {
        parent::__construct('Invalid token', 401);
    }
}
