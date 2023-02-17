<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class ImportFileCorruptException extends Exception
{
    public function __construct($message = 'Import file is corrupted')
    {
        parent::__construct($message);
    }
}
