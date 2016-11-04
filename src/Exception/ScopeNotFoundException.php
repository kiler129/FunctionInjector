<?php


namespace noFlash\FunctionsManipulator\Exception;

use Exception;

class ScopeNotFoundException extends \RuntimeException
{
    public function __construct($scopeId, Exception $previous = null)
    {
        $message = sprintf('There is no scope with ID=%s', (string)$scopeId);

        parent::__construct($message, 0, $previous);
    }
}
