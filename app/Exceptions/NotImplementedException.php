<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class NotImplementedException extends Exception
{
    public function __construct($message = 'Not yet implemented')
    {
        parent::__construct($message);
    }
}
