<?php


namespace noFlash\FunctionsManipulator\Exception;

class InjectionMismatchException extends \LogicException
{
    public function __construct(
        $parameterName,
        $oldValue,
        $newValue,
        \Exception $previous = null)
    {
        $message = sprintf(
            'Injection parameter %s mismatch between instances. ' .
            'Old instance defines "%s" while new defines "%s"',
            $parameterName,
            $oldValue,
            $newValue
        );

        parent::__construct($message, 0, $previous);
    }
}
