<?php

namespace App\Exceptions;

use Exception;

class UrlNotFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct('The shortened URL was not found.', 404);
    }
}
