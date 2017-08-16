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

use noFlash\FunctionsManipulator\Exception\RuntimeException;
use noFlash\FunctionsManipulator\FunctionInjector;
use noFlash\FunctionsManipulator\Injectable\InjectableCallback;
use noFlash\FunctionsManipulator\Injectable\InjectableGlobalReflector;
use noFlash\FunctionsManipulator\Injectable\InjectableInterface;
use noFlash\FunctionsManipulator\InjectionProxy;

class FunctionsInjectorTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FunctionsInjectorTraitFixture
     */
    private $subjectUnderTest;

    /**
     * @var FunctionInjector|\PHPUnit_Framework_MockObject_MockObject
     */
    private $injectorMock;

    public function setUp()
    {
        $this->injectorMock = $this
            ->getMockBuilder(FunctionInjector::class)
            ->getMock()
        ;

        $this->subjectUnderTest = new FunctionsInjectorTraitFixture();
        $this->subjectUnderTest->setCustomInjector($this->injectorMock);
    }

    public function testFunctionCanBeInjectedIntoNamespace()
    {
        $ns = 'noFlash\FunctionsManipulator\Tests\_DemoNS';
        $function = 'demo1Function';
        $cb = function() {};

        $injectionVerifier = function($injection) use ($ns, $function, $cb) {
            $this->assertInstanceOf(InjectableCallback::class, $injection);
            $this->assertSame($ns, $injection->getNamespace());
            $this->assertSame($function, $injection->getFunctionName());
            $this->assertSame($cb, $injection->getCallback());

            return true;
        };

        $this->injectorMock
            ->expects($this->once())
            ->method('injectFunction')
            ->with($this->callback($injectionVerifier))
        ;

        $this->subjectUnderTest->callInjectIntoNamespace($ns, $function, $cb, false);
    }

    public function testFunctionCanBeReplacedInNamespace()
    {
        $ns = 'noFlash\FunctionsManipulator\Tests\_DemoNS';
        $function = 'demo2Function';
        $cb = function() {};

        $injectionVerifier = function($injection) use ($ns, $function, $cb) {
            $this->assertInstanceOf(InjectableCallback::class, $injection);
            $this->assertSame($ns, $injection->getNamespace());
            $this->assertSame($function, $injection->getFunctionName());
            $this->assertSame($cb, $injection->getCallback());

            return true;
        };

        $this->injectorMock
            ->expects($this->once())
            ->method('forceInjectFunction')
            ->with($this->callback($injectionVerifier))
        ;

        $this->subjectUnderTest->callInjectIntoNamespace($ns, $function, $cb, true);
    }

    /**
     * @testdox Injecting into non-existing class scope throws RuntimeException
     */
    public function testInjectingIntoNonExistingClassScopeThrowsRuntimeException()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('class does not exists');

        $this->subjectUnderTest->callInjectIntoClassScope(
            '\noFlash\FunctionsManipulator\Tests\SurelyUnknownClass',
            'testFunction',
            function () {
            }
        );
    }


    public function testFunctionCanBeInjectedIntoClassNamespace()
    {
        $ns = 'noFlash\FunctionsManipulator\Tests';
        $function = 'demo3Function';
        $cb = function() {};

        $injectionVerifier = function($injection) use ($ns, $function, $cb) {
            $this->assertInstanceOf(InjectableCallback::class, $injection);
            $this->assertSame($ns, $injection->getNamespace());
            $this->assertSame($function, $injection->getFunctionName());
            $this->assertSame($cb, $injection->getCallback());

            return true;
        };

        $this->injectorMock
            ->expects($this->once())
            ->method('injectFunction')
            ->with($this->callback($injectionVerifier))
        ;

        $this->subjectUnderTest->callInjectIntoClassScope(
            static::class,
            $function,
            $cb,
            false
        );
    }

    public function testFunctionCanBeReplacedInClassNamespace()
    {
        $ns = 'noFlash\FunctionsManipulator\Tests';
        $function = 'demo4Function';
        $cb = function() {};

        $injectionVerifier = function($injection) use ($ns, $function, $cb) {
            $this->assertInstanceOf(InjectableCallback::class, $injection);
            $this->assertSame($ns, $injection->getNamespace());
            $this->assertSame($function, $injection->getFunctionName());
            $this->assertSame($cb, $injection->getCallback());

            return true;
        };

        $this->injectorMock
            ->expects($this->once())
            ->method('forceInjectFunction')
            ->with($this->callback($injectionVerifier))
        ;

        $this->subjectUnderTest->callInjectIntoClassScope(
            static::class,
            $function,
            $cb,
            true
        );
    }

    /**
     * @uses \noFlash\FunctionsManipulator\InjectionProxy
     * @runInSeparateProcess
     */
    public function testAllKnownProxyInjectionsCanBeReset()
    {
        $timeMock = $this->getMockForAbstractClass(InjectableInterface::class);
        $timeMock
            ->expects($this->atLeastOnce())
            ->method('getFunctionName')
            ->willReturn('time')
        ;
        $timeMock
            ->expects($this->atLeastOnce())
            ->method('getNamespace')
            ->willReturn('noFlash\FunctionsManipulator\Tests\_Time')
        ;

        $dateMock = $this->getMockForAbstractClass(InjectableInterface::class);
        $dateMock
            ->expects($this->atLeastOnce())
            ->method('getFunctionName')
            ->willReturn('date')
        ;
        $dateMock
            ->expects($this->atLeastOnce())
            ->method('getNamespace')
            ->willReturn('noFlash\FunctionsManipulator\Tests\_Date')
        ;

        $randMock = $this->getMockForAbstractClass(InjectableInterface::class);
        $randMock
            ->expects($this->atLeastOnce())
            ->method('getFunctionName')
            ->willReturn('rand')
        ;
        $randMock
            ->expects($this->atLeastOnce())
            ->method('getNamespace')
            ->willReturn('noFlash\FunctionsManipulator\Tests\_Rand')
        ;

        InjectionProxy::setInjection('time-scope', $timeMock);
        InjectionProxy::setInjection('date-scope', $dateMock);
        InjectionProxy::setInjection('rand-scope', $randMock);


        $this->subjectUnderTest->callResetAllInjections();


        $timeReplacement = InjectionProxy::getInjection('time-scope');
        $this->assertInstanceOf(InjectableGlobalReflector::class, $timeReplacement);
        $this->assertSame(
            'noFlash\FunctionsManipulator\Tests\_Time',
            $timeReplacement->getNamespace()
        );
        $this->assertSame('time', $timeReplacement->getFunctionName());

        $dateReplacement = InjectionProxy::getInjection('date-scope');
        $this->assertInstanceOf(InjectableGlobalReflector::class, $dateReplacement);
        $this->assertSame(
            'noFlash\FunctionsManipulator\Tests\_Date',
            $dateReplacement->getNamespace()
        );
        $this->assertSame('date', $dateReplacement->getFunctionName());

        $randReplacement = InjectionProxy::getInjection('rand-scope');
        $this->assertInstanceOf(InjectableGlobalReflector::class, $randReplacement);
        $this->assertSame(
            'noFlash\FunctionsManipulator\Tests\_Rand',
            $randReplacement->getNamespace()
        );
        $this->assertSame('rand', $randReplacement->getFunctionName());
    }
}
