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

use noFlash\FunctionsManipulator\Exception\LogicException;
use noFlash\FunctionsManipulator\Exception\RuntimeException;
use noFlash\FunctionsManipulator\FunctionsInjectorTrait;
use noFlash\FunctionsManipulator\Injectable\InjectableGlobalReflector;
use noFlash\FunctionsManipulator\Injectable\InjectableInterface;
use PHPUnit\Framework\TestCase;

class InjectableGlobalReflectorTest extends TestCase
{
    //Using FunctionInjector to test FunctionInjector - clever, huh? :D
    use FunctionsInjectorTrait;

    /**
     * @var InjectableGlobalReflector
     */
    private $subjectUnderTest;

    public function setUp()
    {
        $this->subjectUnderTest = new InjectableGlobalReflector();
    }

    /**
     * @testdox Generating callback without function name throws LogicException
     */
    public function testGeneratingCallbackWithoutFunctionNameThrowsLogicException()
    {
        $this->expectException(LogicException::class);

        $this->subjectUnderTest->getCallback();
    }

    /**
     * @testdox Getting callback for reflector referencing non-callable global function throws RuntimeException
     */
    public function testGettingCallbackForReflectorReferencingNonCallableGlobalFunctionThrowsRuntimeException()
    {
        $this->subjectUnderTest->setFunctionName('__injectableGlobalReflectorTest__time');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageRegExp(
            '/function __injectableGlobalReflectorTest__time.*?doesn\'t exists/'
        );
        $this->subjectUnderTest->getCallback();
    }

    /**
     * @runInSeparateProcess
     *
     * Note to self: I don't even know if it's possible to call non-global NS one due to
     * PHPs behavior while dealing with callbacks and namespaces: https://3v4l.org/ScceR
     */
    public function testReflectorCallbackCallsGlobalFunction()
    {
        $this->injectIntoNamespace(
            '\noFlash\FunctionsManipulator\Injectable',
            '__injectableGlobalReflectorTest__foo',
            function () {
                $this->fail(
                    'Called function in the scope of the class instead of global one'
                );
            }
        );

        $globalCallsCounter = 0;
        $this->injectIntoNamespace(
            '\\',
            '__injectableGlobalReflectorTest__foo',
            function () use (&$globalCallsCounter) {
                ++$globalCallsCounter;
            }
        );

        $this->subjectUnderTest->setFunctionName('__injectableGlobalReflectorTest__foo');

        $cb = $this->subjectUnderTest->getCallback();
        $cb();

        $this->assertSame(1, $globalCallsCounter);
    }

    /**
     * @runInSeparateProcess
     */
    public function testReflectorPreservesArguments()
    {
        $argString = 'abcdef';
        $argInt = 123;
        $argObj = new \stdClass();

        $this->injectIntoNamespace(
            '\\',
            '__injectableGlobalReflectorTest__args',
            function ($string, $int, $object) use ($argString, $argInt, $argObj) {
                $this->assertSame($argString, $string);
                $this->assertSame($argInt, $int);
                $this->assertSame($argObj, $object);
            }
        );

        $this->subjectUnderTest->setFunctionName('__injectableGlobalReflectorTest__args');
        $cb = $this->subjectUnderTest->getCallback();
        $cb($argString, $argInt, $argObj);
    }

    /**
     * @runInSeparateProcess
     */
    public function testReflectorPreservesReturnValue()
    {
        $expectedReturn = 'Foo Bar Return';

        $this->injectIntoNamespace(
            '\\',
            '__injectableGlobalReflectorTest__ret',
            function () use ($expectedReturn) {
                return $expectedReturn;
            }
        );

        $this->subjectUnderTest->setFunctionName('__injectableGlobalReflectorTest__ret');
        $cb = $this->subjectUnderTest->getCallback();

        $this->assertSame($expectedReturn, $cb());
    }

    public function testCreatingReflectorFromInjectableProducesReflector()
    {
        $injectable = $this->getMockForAbstractClass(InjectableInterface::class);
        $injectable
            ->expects($this->any())
            ->method('getFunctionName')
            ->willReturn('instanceTest')
        ;
        $injectable
            ->expects($this->any())
            ->method('getNamespace')
            ->willReturn('noFlash\FunctionsManipulator\Tests\_InstanceTest')
        ;

        $obj = InjectableGlobalReflector::createReflectorFromInjectable($injectable);

        $this->assertInstanceOf(InjectableGlobalReflector::class, $obj);
    }

    public function testCreatingReflectorFromInjectablePreservesNamespace()
    {
        $injectable = $this->getMockForAbstractClass(InjectableInterface::class);
        $injectable
            ->expects($this->any())
            ->method('getFunctionName')
            ->willReturn('nsTest')
        ;
        $injectable
            ->expects($this->any())
            ->method('getNamespace')
            ->willReturn('noFlash\FunctionsManipulator\Tests\_NSTest')
        ;

        $obj = InjectableGlobalReflector::createReflectorFromInjectable($injectable);

        $this->assertSame(
            'noFlash\FunctionsManipulator\Tests\_NSTest',
            $obj->getNamespace()
        );
    }


    public function testCreatingReflectorFromInjectablePreservesFunctionName()
    {
        $injectable = $this->getMockForAbstractClass(InjectableInterface::class);
        $injectable
            ->expects($this->any())
            ->method('getFunctionName')
            ->willReturn('fnTest')
        ;
        $injectable
            ->expects($this->any())
            ->method('getNamespace')
            ->willReturn('noFlash\FunctionsManipulator\Tests\_FNTest')
        ;

        $obj = InjectableGlobalReflector::createReflectorFromInjectable($injectable);

        $this->assertSame('fnTest', $obj->getFunctionName());
    }
}
