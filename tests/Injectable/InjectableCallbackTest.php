<?php
/**
 * This file is part of FunctionInjector project.
 * You are using it at your own risk and you are fully responsible for everything that code will do.
 *
 * Copyright (c) 2016 Grzegorz Zdanowski <grzegorz@noflash.pl>
 *
 * For the full copyright and license information, please view the LICENSE file distributed with this source code.
 */

namespace noFlash\FunctionsManipulator\Tests\Injectable;

use noFlash\FunctionsManipulator\Exception\IncompleteInjectableException;
use noFlash\FunctionsManipulator\Exception\InvalidFunctionNameException;
use noFlash\FunctionsManipulator\Exception\InvalidNamespaceException;
use noFlash\FunctionsManipulator\Injectable\InjectableCallback;
use noFlash\FunctionsManipulator\Injectable\InjectableInterface;
use PHPUnit\Framework\TestCase;

class InjectableCallbackTest extends TestCase
{
    /**
     * @var InjectableCallback
     */
    private $subjectUnderTest;

    public function setUp()
    {
        $this->subjectUnderTest = new InjectableCallback();
    }

    /**
     * @testdox Class implements InjectableInterface
     */
    public function testClassImplementsInjectableInterface()
    {
        $this->assertTrue($this->subjectUnderTest instanceof InjectableInterface);
    }

    public function testNamespaceIsNotSetByDefault()
    {
        $this->assertNull($this->subjectUnderTest->getNamespace());
    }

    public function testFunctionNameIsNotSetByDefault()
    {
        $this->assertNull($this->subjectUnderTest->getFunctionName());
    }

    /**
     * @testdox Default callback throws IncompleteInjectableException
     */
    public function testDefaultCallbackThrowsIncompleteInjectableException()
    {
        $cb = $this->subjectUnderTest->getCallback();
        $this->assertTrue(is_callable($cb), 'Returned entity is not callable');

        $this->expectException(IncompleteInjectableException::class);

        call_user_func($cb);
    }

    public function validNamespaceProvider()
    {
        return [
            [''],
            ['\\'],
            ['\\foo'],
            ['\\bar\\'],
            ['baz\\'],
        ];
    }

    /**
     * @dataProvider validNamespaceProvider
     */
    public function testValidNamespaceIsSettable($ns)
    {
        $this->assertSame(
            $this->subjectUnderTest,
            $this->subjectUnderTest->setNamespace($ns),
            'Setter is not fluent'
        );

        $this->assertSame($ns, $this->subjectUnderTest->getNamespace());
    }

    public function testExceptionIsThrownIfNamespaceNameIsNotAString()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->subjectUnderTest->setNamespace(M_PI);
    }

    public function reservedKeywordsNamespaceProvider()
    {
        return [
            ['class'],
            ['foo\\isset'],
            ['foo\\namespace\\bar'],
            ['baz\\throw']
        ];
    }

    /**
     * @dataProvider reservedKeywordsNamespaceProvider
     */
    public function testExceptionIsThrownForNamespacesContainingReservedKeywords($ns)
    {
        $this->expectException(InvalidNamespaceException::class);
        $this->expectExceptionMessage('reserved');

        $this->subjectUnderTest->setNamespace($ns);
    }

    public function testExceptionIsThrownForNamespacesNotConformingWithPhpRules()
    {
        $this->expectException(InvalidNamespaceException::class);
        $this->expectExceptionMessage('regex');

        $this->subjectUnderTest->setNamespace('\\foo\\bar \\baz');
    }

    public function validFunctionNamesProvider()
    {
        return [
            ['test'],
            ['foo'],
            ['foo_bar'],
            ['foo_baz_123'],
        ];
    }

    /**
     * @dataProvider validFunctionNamesProvider
     */
    public function testValidFunctionNameIsSettable($name)
    {
        $this->assertSame(
            $this->subjectUnderTest,
            $this->subjectUnderTest->setFunctionName($name),
            'Setter is not fluent'
        );

        $this->assertSame($name, $this->subjectUnderTest->getFunctionName());
    }

    public function testExceptionIsThrownIfFunctionNameIsNotAString()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->subjectUnderTest->setFunctionName(123);
    }

    public function reservedKeywordsFunctionsProvider()
    {
        return [
            ['class'],
            ['trait'],
            ['class'],
            ['die']
        ];
    }

    /**
     * @dataProvider reservedKeywordsFunctionsProvider
     */
    public function testExceptionIsThrownForFunctionNameBeingAReservedKeyword($name)
    {
        $this->expectException(InvalidFunctionNameException::class);
        $this->expectExceptionMessage('reserved');

        $this->subjectUnderTest->setFunctionName($name);
    }

    public function testExceptionIsThrownForFunctionNameNotConformingWithPhpRules()
    {
        $this->expectException(InvalidFunctionNameException::class);
        $this->expectExceptionMessage('regex');

        $this->subjectUnderTest->setFunctionName('this is test');
    }

    public function validCallbacksProvider()
    {
        return [
            ['sprintf'],
            ['\Closure::bind'],
            [function(){}]
        ];
    }

    /**
     * @dataProvider validCallbacksProvider
     */
    public function testValidCallbackIsSettable($cb)
    {
        $this->assertSame(
            $this->subjectUnderTest,
            $this->subjectUnderTest->setCallback($cb),
            'Setter is not fluent'
        );

        $this->assertSame($cb, $this->subjectUnderTest->getCallback());
    }
}
