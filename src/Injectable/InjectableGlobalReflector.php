<?php
/**
 * This file is part of FunctionInjector project.
 * You are using it at your own risk and you are fully responsible for everything that
 * code will do.
 *
 * Copyright (c) 2017 Grzegorz Zdanowski <grzegorz@noflash.pl>
 *
 * For the full copyright and license information, please view the LICENSE file
 * distributed with this source code.
 */

namespace noFlash\FunctionsManipulator\Injectable;

use noFlash\FunctionsManipulator\Exception\RuntimeException;

/**
 * This injectable simply creates reflector which if called simply calls global function.
 */
class InjectableGlobalReflector extends AbstractInjectable
{
    /**
     * @var callable
     */
    private $callback;

    private function generateReflectorCallback()
    {
        $fn = $this->getFunctionName();
        if ($fn === null) {
            throw new \LogicException(
                'Reflector callback cannot be generate until function name is set'
            );
        }

        $globalFn = '\\' . $fn;
        if (!is_callable($globalFn)) {
            throw new RuntimeException(
                sprintf(
                    'Failed to generate reflector callback - ' .
                    'function %s() doesn\'t exists in the global scope',
                    $this->getFunctionName()
                )
            );
        }

        return function (...$args) use ($globalFn) {
            return $globalFn(...$args);
        };
    }

    public function getCallback()
    {
        if ($this->callback === null) {
            $this->callback = $this->generateReflectorCallback();
        }

        return $this->callback;
    }

    public static function createReflectorFromInjectable(InjectableInterface $injectable)
    {
        $reflector = new static();
        $reflector
            ->setNamespace($injectable->getNamespace())
            ->setFunctionName($injectable->getFunctionName())
        ;

        return $reflector;
    }
}
