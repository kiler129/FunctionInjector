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

class InjectableCallback implements InjectableInterface
{
    /**
     * @var NameValidator
     */
    private $nameValidator;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string
     */
    private $functionName;

    /**
     * @var callable
     */
    private $callback;

    public function __construct()
    {
        $this->nameValidator = new NameValidator();
        $this->callback = function () {
            throw new IncompleteInjectableException(
                'Injectable callback was invoked, but no callback was set'
            );
        };
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function setNamespace($ns)
    {
        if (!is_string($ns)) {
            throw new \InvalidArgumentException(
                "Namespace expected to be a string, got $ns"
            );
        }

        $valid = $this->nameValidator->validateNamespace($ns);
        if ($valid !== NameValidator::RESULT_OK) {
            throw new InvalidNamespaceException($ns, $valid);
        }

        $this->namespace = $ns;

        return $this;
    }

    public function getFunctionName()
    {
        return $this->functionName;
    }

    public function setFunctionName($name)
    {
        $valid = $this->nameValidator->validateFunctionName($name);
        if ($valid !== NameValidator::RESULT_OK) {
            throw new InvalidFunctionNameException($name, $valid);
        }

        $this->functionName = $name;

        return $this;
    }

    public function getCallback()
    {
        return $this->callback;
    }

    public function setCallback(callable $callback)
    {
        $this->callback = $callback;

        return $this;
    }
}
