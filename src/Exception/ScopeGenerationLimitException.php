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
