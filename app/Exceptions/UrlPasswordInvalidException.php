<?php

namespace App\Exceptions;

use Exception;

class UrlPasswordInvalidException extends Exception
{
    public function __construct()
    {
        parent::__construct('Invalid password for the shortened URL.', 403);
    }
}
