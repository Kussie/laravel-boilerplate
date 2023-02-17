<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class UserDoesNotExistException extends Exception
{
    public function __construct($message = 'User does not exist')
    {
        parent::__construct($message);
    }
}
