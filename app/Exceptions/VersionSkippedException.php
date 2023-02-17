<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class VersionSkippedException extends Exception
{
    public function __construct($message = 'This package would result in a skipped version of the ruleset')
    {
        parent::__construct($message);
    }
}
