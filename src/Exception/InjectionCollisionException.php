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

class InjectionCollisionException extends \LogicException
{
    public function __construct($namespace, $functionName, \Exception $previous = null)
    {
        if (empty($namespace)) {
            $namespace = 'global namespace';
        }
        
        $message = sprintf(
            'Function %s cannot be injected into %s - there is another injection in this scope',
            $functionName,
            $namespace
        );

        parent::__construct($message, 0, $previous);
    }
}
