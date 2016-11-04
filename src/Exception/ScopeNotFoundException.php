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

class ScopeNotFoundException extends \RuntimeException
{
    public function __construct($scopeId, Exception $previous = null)
    {
        $message = sprintf('There is no scope with ID=%s', (string)$scopeId);

        parent::__construct($message, 0, $previous);
    }
}
