<?php

namespace App\Exceptions;

use Exception;

class ShortUrlExistException extends Exception
{
    public function __construct($message = "Short URL already exists", $code = 409)
    {
        parent::__construct($message, $code);
    }
}
