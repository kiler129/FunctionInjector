<?php


namespace noFlash\FunctionsManipulator\Exception;

use Exception;
use noFlash\FunctionsManipulator\NameValidator;

class InvalidFunctionNameException extends \InvalidArgumentException
{
    public function __construct($functionName, $reasonCode, Exception $previous = null)
    {
        $message = sprintf(
            'Function name "%s" is invalid - %s.',
            $functionName,
            NameValidator::getErrorFromCode($reasonCode)
        );

        parent::__construct($message, 0, $previous);
    }
}
