<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class MalformedContentException extends Exception
{
    public function __construct($message = 'Malformed Content Encountered')
    {
        parent::__construct($message);
    }
}
