<?php
/**
 * This file is part of FunctionInjector project.
 * You are using it at your own risk and you are fully responsible for everything that code will do.
 *
 * Copyright (c) 2017 Grzegorz Zdanowski <grzegorz@noflash.pl>
 *
 * For the full copyright and license information, please view the LICENSE file distributed with this source code.
 */

namespace noFlash\FunctionsManipulator\Tests;

use noFlash\FunctionsManipulator\FunctionInjector;
use noFlash\FunctionsManipulator\FunctionsInjectorTrait;

class FunctionsInjectorTraitFixture
{
    use FunctionsInjectorTrait;

    public function callInjectIntoClassScope(...$args)
    {
        $this->injectIntoClassScope(...$args);
    }

    public function callInjectIntoNamespace(...$args)
    {
        $this->injectIntoNamespace(...$args);
    }

    public function callResetAllInjections()
    {
        $this->resetAllInjections();
    }

    public function setCustomInjector(FunctionInjector $injector)
    {
        //Not a very elegant solution to mess like that with trait, but I don't have
        // a better idea how to test that without doing real injections etc.
        $this->_injector = $injector;
    }
}
