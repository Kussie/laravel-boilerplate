<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class VersionAlreadyExistsException extends Exception
{
    public function __construct($message = 'Version already exists')
    {
        parent::__construct($message);
    }
}
