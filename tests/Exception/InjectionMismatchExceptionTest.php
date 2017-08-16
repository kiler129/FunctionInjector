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

use noFlash\FunctionsManipulator\Exception\InjectionMismatchException;
use PHPUnit\Framework\TestCase;

class InjectionMismatchExceptionTest extends TestCase
{
    public function testClassIsAnException()
    {
        $this->assertTrue(
            is_subclass_of(InjectionMismatchException::class, \Exception::class)
        );
    }

    public function testGeneratedMessageContainsParameterName()
    {
        $parameterName = sha1(microtime(true) . mt_rand());
        $sut = new InjectionMismatchException($parameterName, null, null);

        $this->assertContains($parameterName, $sut->getMessage());
    }

    public function testGeneratedMessageContainsOldValue()
    {
        $oldVal = sha1(microtime(true) . mt_rand());
        $sut = new InjectionMismatchException(null, $oldVal, null);

        $this->assertContains($oldVal, $sut->getMessage());
    }

    public function testGeneratedMessageContainsNewValue()
    {
        $newVal = sha1(microtime(true) . mt_rand());
        $sut = new InjectionMismatchException(null, null, $newVal);

        $this->assertContains($newVal, $sut->getMessage());
    }
}
