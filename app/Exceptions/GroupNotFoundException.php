<?php

namespace App\Exceptions;

use Exception;

class GroupNotFoundException extends Exception
{
    public function __construct($message = 'Group not found', $code = 404)
    {
        parent::__construct($message, $code, );
    }
}
