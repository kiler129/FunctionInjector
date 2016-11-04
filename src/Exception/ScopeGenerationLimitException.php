<?php


namespace noFlash\FunctionsManipulator\Exception;

class ScopeGenerationLimitException extends \RuntimeException
{
    public function __construct($limit, \Exception $previous = null)
    {
        $message = sprintf(
            'Scope ID generator exhausted allowed limit of %d attempts',
            $limit
        );

        parent::__construct($message, 0, $previous);
    }
}
