<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class UnexpectedRuleSetException extends Exception
{
    public function __construct($message = 'Unexpected rule set')
    {
        parent::__construct($message);
    }
}
