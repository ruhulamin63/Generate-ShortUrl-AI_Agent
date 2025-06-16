<?php

namespace App\Exceptions;

use Exception;

class TokenNotProvidedException extends Exception
{
    public function __construct()
    {
        parent::__construct('Token not provided', 401);
    }
}
