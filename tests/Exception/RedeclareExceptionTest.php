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

use noFlash\FunctionsManipulator\Exception\RedeclareException;

class RedeclareExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsAnException()
    {
        $this->assertTrue(
            is_subclass_of(RedeclareException::class, \Exception::class)
        );
    }

    public function testGeneratedMessageContainsNamespace()
    {
        $ns = sha1(microtime(true) . mt_rand());
        $sut = new RedeclareException($ns, null);

        $this->assertContains($ns, $sut->getMessage());
    }

    public function testGeneratedMessageContainsGlobalNamespaceNoteIfNamespaceWasEmpty()
    {
        $sut = new RedeclareException('', null);

        $this->assertContains('global namespace', $sut->getMessage());
    }
    
    public function testGeneratedMessageContainsFunctionName()
    {
        $functionName = sha1(microtime(true) . mt_rand());
        $sut = new RedeclareException(null, $functionName);

        $this->assertContains($functionName, $sut->getMessage());
    }

    public function testGeneratedMessageContainsInformationAboutCollisionReason()
    {
        $sut = new RedeclareException(null, null);

        $this->assertContains('function already defined', $sut->getMessage());
    }
}
