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

use noFlash\FunctionsManipulator\Exception\ScopeNotFoundException;

class ScopeNotFoundExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsAnException()
    {
        $this->assertTrue(
            is_subclass_of(ScopeNotFoundException::class, \Exception::class)
        );
    }

    public function testGeneratedMessageContainsScopeId()
    {
        $scopeId = sha1(uniqid());
        $expected = "ID=$scopeId";

        $sut = new ScopeNotFoundException($scopeId);
        $this->assertContains($expected, $sut->getMessage());
    }
}
