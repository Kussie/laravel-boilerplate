<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class ImportFileCouldNotBeExtractedException extends Exception
{
    public function __construct($message = 'Import file could not be extracted')
    {
        parent::__construct($message);
    }
}
