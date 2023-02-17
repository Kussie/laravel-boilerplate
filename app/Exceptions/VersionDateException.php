<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class VersionDateException extends Exception
{
    public function __construct($message = 'Previous Version end date would be before the start date')
    {
        parent::__construct($message);
    }
}
