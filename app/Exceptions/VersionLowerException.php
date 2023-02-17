<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class VersionLowerException extends Exception
{
    public function __construct($message = 'The package has a lower version then currently allowed in DRA')
    {
        parent::__construct($message);
    }
}
