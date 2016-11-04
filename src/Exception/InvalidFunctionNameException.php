<?php
/**
 * This file is part of FunctionInjector project.
 * You are using it at your own risk and you are fully responsible for everything that code will do.
 *
 * Copyright (c) 2016 Grzegorz Zdanowski <grzegorz@noflash.pl>
 *
 * For the full copyright and license information, please view the LICENSE file distributed with this source code.
 */

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
