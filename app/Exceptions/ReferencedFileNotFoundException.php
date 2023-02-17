<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class ReferencedFileNotFoundException extends Exception
{
    public function __construct($message = 'Referenced file not found')
    {
        parent::__construct($message);
    }
}
