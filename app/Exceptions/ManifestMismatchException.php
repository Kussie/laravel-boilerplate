<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class ManifestMismatchException extends Exception
{
    public function __construct($message = 'Manifest file mismatch')
    {
        parent::__construct($message);
    }
}
