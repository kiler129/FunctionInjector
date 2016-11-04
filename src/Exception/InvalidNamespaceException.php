<?php


namespace noFlash\FunctionsManipulator\Exception;

use Exception;
use noFlash\FunctionsManipulator\NameValidator;

class InvalidNamespaceException extends \InvalidArgumentException
{
    public function __construct($namespace, $reasonCode, Exception $previous = null)
    {
        $message = sprintf(
            'Namespace "%s" is invalid - %s.',
            $namespace,
            NameValidator::getErrorFromCode($reasonCode)
        );

        parent::__construct($message, 0, $previous);
    }
}
