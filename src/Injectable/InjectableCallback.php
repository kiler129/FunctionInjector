<?php
/**
 * This file is part of FunctionInjector project.
 * You are using it at your own risk and you are fully responsible for everything that code will do.
 *
 * Copyright (c) 2016 Grzegorz Zdanowski <grzegorz@noflash.pl>
 *
 * For the full copyright and license information, please view the LICENSE file distributed with this source code.
 */

namespace noFlash\FunctionsManipulator\Injectable;

use noFlash\FunctionsManipulator\Exception\IncompleteInjectableException;
use noFlash\FunctionsManipulator\Exception\InvalidFunctionNameException;
use noFlash\FunctionsManipulator\Exception\InvalidNamespaceException;
use noFlash\FunctionsManipulator\NameValidator;

class InjectableCallback extends AbstractInjectable
{
    /**
     * @var callable
     */
    private $callback;

    public function getCallback()
    {
        if ($this->callback === null) {
            return function () {
                throw new IncompleteInjectableException(
                    'Injectable callback was invoked, but no callback was set'
                );
            };
        }

        return $this->callback;
    }

    public function setCallback(callable $callback)
    {
        $this->callback = $callback;

        return $this;
    }
}
