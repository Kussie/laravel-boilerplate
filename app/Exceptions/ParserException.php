<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class ParserException extends Exception
{
    public function __construct($message = 'Parser Exception')
    {
        parent::__construct($message);
    }
}
