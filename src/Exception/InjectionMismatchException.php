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
