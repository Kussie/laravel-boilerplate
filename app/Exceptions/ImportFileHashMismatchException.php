<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class ImportFileHashMismatchException extends Exception
{
    public function __construct($message = 'Import file hash mismatch')
    {
        parent::__construct($message);
    }
}
