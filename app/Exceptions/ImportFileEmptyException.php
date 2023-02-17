<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class ImportFileEmptyException extends Exception
{
    public function __construct($message = 'Import file is empty')
    {
        parent::__construct($message);
    }
}
