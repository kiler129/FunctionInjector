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

namespace noFlash\FunctionsManipulator\Tests\Injectable;

use noFlash\FunctionsManipulator\Exception\InvalidArgumentException;
use noFlash\FunctionsManipulator\Exception\InvalidFunctionNameException;
use noFlash\FunctionsManipulator\Exception\InvalidNamespaceException;

class AbstractInjectableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractInjectableFixture
     */
    private $subjectUnderTest;

    public function setUp()
    {
        $this->subjectUnderTest = new AbstractInjectableFixture();
    }

    public function testDefaultNamespaceIsNull()
    {
        $this->assertNull($this->subjectUnderTest->getNamespace());
    }

    public function testDefaultFunctionNameIsNull()
    {
        $this->assertNull($this->subjectUnderTest->getFunctionName());
    }

    public function testValidNamespaceCanBeSet()
    {
        $ns = 'noFlash\FunctionsManipulator\Tests\Injectable';

        $this->subjectUnderTest->setNamespace($ns);
        $this->assertSame($ns, $this->subjectUnderTest->getNamespace());
    }

    /**
     * @testdox Passing non-string namespace throws InvalidArgumentException
     */
    public function testPassingNonStringNamespaceThrowsInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('/expected.*?string.*?got double/');

        $this->subjectUnderTest->setNamespace((double)123);
    }

    /**
     * @testdox Passing invalid namespace throws InvalidNamespaceException
     */
    public function testPassingInvalidNamespaceThrowsInvalidNamespaceException()
    {
        $this->expectException(InvalidNamespaceException::class);

        $this->subjectUnderTest->setNamespace('Foo\\Bar Baz');
    }

    public function testValidFunctionNameCanBeSet()
    {
        $fn = 'fooBar';

        $this->subjectUnderTest->setFunctionName($fn);
        $this->assertSame($fn, $this->subjectUnderTest->getFunctionName());
    }

    /**
     * @testdox Passing invalid function name throws InvalidFunctionNameException
     */
    public function testPassingInvalidFunctionNameThrowsInvalidFunctionNameException()
    {
        $this->expectException(InvalidFunctionNameException::class);

        $this->subjectUnderTest->setFunctionName('foo Bar');
    }
}
