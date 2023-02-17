<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class MalformedGlossaryContentException extends Exception
{
    public function __construct($message = 'Malformed glossary definition in XHTML file')
    {
        parent::__construct($message);
    }
}
