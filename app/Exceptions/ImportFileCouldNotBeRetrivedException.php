<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class ImportFileCouldNotBeRetrivedException extends Exception
{
    public function __construct($message = 'Import file could not be retrieved')
    {
        parent::__construct($message);
    }
}
