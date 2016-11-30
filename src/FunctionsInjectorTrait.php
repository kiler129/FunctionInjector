<?php
/**
 * This file is part of FunctionInjector project.
 * You are using it at your own risk and you are fully responsible for everything that code will do.
 *
 * Copyright (c) 2016 Grzegorz Zdanowski <grzegorz@noflash.pl>
 *
 * For the full copyright and license information, please view the LICENSE file distributed with this source code.
 */

namespace noFlash\FunctionsManipulator;

use noFlash\FunctionsManipulator\Injectable\InjectableCallback;

trait FunctionsInjectorTrait
{
    /**
     * @var FunctionInjector
     */
    private $_injector;

    protected function injectIntoClassScope($fqcn, $function, callable $cb, $replaceIfExists = false)
    {
        if (!class_exists($fqcn)) {
            throw new \RuntimeException(
                sprintf(
                    'Cannot inject "%s()" into "%s" scope - class does not exists',
                    $function,
                    $fqcn
                )
            );
        }

        $fqcn = trim($fqcn, '\\');
        $ns = substr($fqcn, 0, (int)strrpos($fqcn, '\\'));

        $this->injectIntoNamespace($ns, $function, $cb, $replaceIfExists);
    }

    protected function injectIntoNamespace($ns, $function, callable $cb, $replaceIfExists = false)
    {
        if ($this->_injector === null) {
            $this->_injector = new FunctionInjector();
        }

        $injection = new InjectableCallback();
        $injection->setNamespace($ns);
        $injection->setFunctionName($function);
        $injection->setCallback($cb);

        if ($replaceIfExists) {
            $this->_injector->forceInjectFunction($injection);
            
        } else {
            $this->_injector->injectFunction($injection);
        }
    }
}
