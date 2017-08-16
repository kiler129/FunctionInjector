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

use noFlash\FunctionsManipulator\Exception\InvalidNamespaceException;
use noFlash\FunctionsManipulator\NameValidator;
use PHPUnit\Framework\TestCase;

class InvalidNamespaceExceptionTest extends TestCase
{
    public function testClassIsAnException()
    {
        $this->assertTrue(
            is_subclass_of(InvalidNamespaceException::class, \Exception::class)
        );
    }

    public function testGeneratedMessageContainsNamespaceName()
    {
        $namespaceName = sha1(microtime(true) . mt_rand());
        $sut = new InvalidNamespaceException($namespaceName, 0);

        $this->assertContains($namespaceName, $sut->getMessage());
    }

    public function errorCodesProvider()
    {
        return [
            [NameValidator::RESULT_OK, NameValidator::getErrorFromCode(NameValidator::RESULT_OK)],
            [NameValidator::RESULT_RESERVED_KEYWORD, NameValidator::getErrorFromCode(NameValidator::RESULT_RESERVED_KEYWORD)],
            [NameValidator::RESULT_REGEX_FAILED, NameValidator::getErrorFromCode(NameValidator::RESULT_REGEX_FAILED)]
        ];
    }

    /**
     * @dataProvider errorCodesProvider
     */
    public function testGeneratedMessageContainsReasonCodeText($code, $msg)
    {
        $sut = new InvalidNamespaceException($msg, $code);
        $this->assertContains($msg, $sut->getMessage());
    }
}
