<?php
/**
 * This file is part of FunctionInjector project.
 * You are using it at your own risk and you are fully responsible for everything that code will do.
 *
 * Copyright (c) 2016 Grzegorz Zdanowski <grzegorz@noflash.pl>
 *
 * For the full copyright and license information, please view the LICENSE file distributed with this source code.
 */

namespace noFlash\FunctionsManipulator\Tests\Exception;

use noFlash\FunctionsManipulator\Exception\IncompleteInjectableException;
use PHPUnit\Framework\TestCase;

class IncompleteInjectableExceptionTest extends TestCase
{
    public function testClassIsAnException()
    {
        $this->assertTrue(
            is_subclass_of(IncompleteInjectableException::class, \Exception::class)
        );
    }
}
