<?php
/**
 * This file is part of FunctionInjector project.
 * You are using it at your own risk and you are fully responsible for everything that code will do.
 *
 * Copyright (c) 2017 Grzegorz Zdanowski <grzegorz@noflash.pl>
 *
 * For the full copyright and license information, please view the LICENSE file distributed with this source code.
 */

namespace noFlash\FunctionsManipulator\Injectable;

use noFlash\FunctionsManipulator\Exception\InvalidFunctionNameException;
use noFlash\FunctionsManipulator\Exception\InvalidNamespaceException;
use noFlash\FunctionsManipulator\NameValidator;

abstract class AbstractInjectable implements InjectableInterface
{
    /**
     * @var NameValidator
     */
    private $nameValidator;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var string
     */
    protected $functionName;

    public function __construct()
    {
        $this->nameValidator = new NameValidator();
    }

    /**
     * @inheritdoc
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    public function setNamespace($ns)
    {
        if (!is_string($ns)) {
            throw new \InvalidArgumentException(
                "Namespace expected to be a string, got " . gettype($ns)
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
}
