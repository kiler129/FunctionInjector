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

namespace noFlash\FunctionsManipulator\Tests;

use noFlash\FunctionsManipulator\Exception\ScopeNotFoundException;
use noFlash\FunctionsManipulator\Injectable\InjectableInterface;
use noFlash\FunctionsManipulator\InjectionProxy;

/**
 * @runTestsInSeparateProcesses
 */
class InjectionProxyTest extends \PHPUnit_Framework_TestCase
{
    public function testNewInstanceCannotBeCreated()
    {
        $proxyRef = new \ReflectionClass(InjectionProxy::class);

        $this->assertFalse($proxyRef->isInstantiable());
    }

    public function testInjectionCanBeSetWithGivenScope()
    {
        $injMock = $this->getMockForAbstractClass(InjectableInterface::class);

        InjectionProxy::setInjection('test-scope-id', $injMock);

        $this->assertTrue(InjectionProxy::hasInjection('test-scope-id'));
        $this->assertSame($injMock, InjectionProxy::getInjection('test-scope-id'));
    }

    public function testSettingDifferentInjetionInTheSameScopeReplacesPreviousInjection()
    {
        $injMock1 = $this->getMockForAbstractClass(InjectableInterface::class);
        $injMock2 = $this->getMockForAbstractClass(InjectableInterface::class);
        $injMock3 = $this->getMockForAbstractClass(InjectableInterface::class);

        InjectionProxy::setInjection('foo-scope', $injMock1);
        InjectionProxy::setInjection('foo-scope', $injMock2);
        InjectionProxy::setInjection('foo-scope', $injMock3);

        $this->assertSame($injMock3, InjectionProxy::getInjection('foo-scope'));
    }

    public function testCheckingForUnknownScopeReturnsFalse()
    {
        $this->assertFalse(InjectionProxy::hasInjection('unknown-scope'));
    }

    public function testThereIsNoInjectionsByDefault()
    {
        $this->assertEmpty(InjectionProxy::getInjections());
    }

    public function testAllInjectionsCanBeListedWithTheirScopes()
    {
        $injMock1 = $this->getMockForAbstractClass(InjectableInterface::class);
        $injMock2 = $this->getMockForAbstractClass(InjectableInterface::class);
        $injMock3 = $this->getMockForAbstractClass(InjectableInterface::class);

        InjectionProxy::setInjection('foo', $injMock1);
        InjectionProxy::setInjection('bar', $injMock2);
        InjectionProxy::setInjection('baz', $injMock3);


        $injections = InjectionProxy::getInjections();
        $this->assertCount(3, $injections);

        $this->assertArrayHasKey('foo', $injections);
        $this->assertSame($injMock1, $injections['foo']);

        $this->assertArrayHasKey('bar', $injections);
        $this->assertSame($injMock2, $injections['bar']);

        $this->assertArrayHasKey('baz', $injections);
        $this->assertSame($injMock3, $injections['baz']);
    }

    /**
     * @testdox Getting unknown injection throws ScopeNotFoundException
     */
    public function testGettingUnknownInjectionThrowsScopeNotFoundException()
    {
        $this->expectException(ScopeNotFoundException::class);
        $this->expectExceptionMessageRegExp('/yyyyyyyyy/');

        InjectionProxy::getInjection('yyyyyyyyy');
    }

    public function testInjectableCallbackIsCalledWithProperArguments()
    {
        $arguments = ['foo', 123, M_PI, new \stdClass()];
        $expectedReturn = 'test value';

        $callbackCalledTimes = 0;
        $callback = function (...$args) use (
            $arguments,
            $expectedReturn,
            &$callbackCalledTimes
        ) {
            ++$callbackCalledTimes;
            $this->assertSame($arguments, $args);

            return 'test value';
        };

        $injectableMock = $this->getMockForAbstractClass(InjectableInterface::class);
        $injectableMock
            ->expects($this->atLeastOnce())
            ->method('getCallback')
            ->willReturn($callback)
        ;

        InjectionProxy::setInjection('beer-scope', $injectableMock);

        $result = InjectionProxy::callInjection('beer-scope', $arguments);

        $this->assertSame($expectedReturn, $result);
        $this->assertSame(1, $callbackCalledTimes);
    }


}
