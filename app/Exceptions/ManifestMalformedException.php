<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class ManifestMalformedException extends Exception
{
    public function __construct($message = 'Manifest File Malformed')
    {
        parent::__construct($message);
    }
}
