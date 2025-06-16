<?php

namespace App\Exceptions;

use Exception;

class UserNotFound extends Exception
{
    public function __construct()
    {
        parent::__construct('User not found', 404);
    }
}
