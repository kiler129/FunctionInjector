<?php
/**
 * This file is part of FunctionInjector project.
 * You are using it at your own risk and you are fully responsible for everything that code will do.
 *
 * Copyright (c) 2016 Grzegorz Zdanowski <grzegorz@noflash.pl>
 *
 * For the full copyright and license information, please view the LICENSE file distributed with this source code.
 */

namespace noFlash\FunctionsManipulator\Tests;

use noFlash\FunctionsManipulator\Exception\IncompleteInjectableException;
use noFlash\FunctionsManipulator\FunctionInjector;
use noFlash\FunctionsManipulator\Injectable\InjectableInterface;

class FunctionInjectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FunctionInjector
     */
    private $subjectUnderTest;

    public function setUp()
    {
        $this->subjectUnderTest = new FunctionInjector();
    }

    public function testAttemptingToInjectEntityWithoutNameThrowsAnException()
    {
        $injection = $this->getMockForAbstractClass(InjectableInterface::class);
        $injection
            ->expects($this->atLeastOnce())
            ->method('getFunctionName')
            ->willReturn(null);

        $this->setExpectedException(
            IncompleteInjectableException::class,
            'function name'
        );
        $this->subjectUnderTest->injectFunction($injection);
    }

    public function testClosureCanBeInjected()
    {
        $closure = function () {};

        $ns = 'noFlash\FunctionsManipulator\Tests\Simulation_testClosureInj';
        $fn = 'testFunctionName';

        $injection = $this->getMockForAbstractClass(InjectableInterface::class);
        $injection
            ->expects($this->atLeastOnce())
            ->method('getFunctionName')
            ->willReturn($fn);
        $injection
            ->expects($this->atLeastOnce())
            ->method('getNamespace')
            ->willReturn($ns);
        $injection
            ->expects($this->any())
            ->method('getCallback')
            ->willReturn($closure);

        $this->assertInternalType(
            'string',
            $this->subjectUnderTest->injectFunction($injection)
        );
    }

    public function testInjectedClosureIsExecutable()
    {
        $incrementer = 0;

        $closure = function () use (&$incrementer) {
            $incrementer++;
        };

        $ns = 'noFlash\FunctionsManipulator\Tests\Simulation_testClosureExe';
        $fn = 'testFunctionName';
        $accessor = $ns . '\\' . $fn;

        $injection = $this->getMockForAbstractClass(InjectableInterface::class);
        $injection
            ->expects($this->atLeastOnce())
            ->method('getFunctionName')
            ->willReturn($fn);
        $injection
            ->expects($this->atLeastOnce())
            ->method('getNamespace')
            ->willReturn($ns);
        $injection
            ->expects($this->any())
            ->method('getCallback')
            ->willReturn($closure);

        $this->subjectUnderTest->injectFunction($injection);
        $this->assertTrue(
            function_exists($accessor),
            'Expected function to be injected at ' . $accessor
        );
        $this->assertSame(0, $incrementer, 'Callback got executed during injection');

        $accessor(); //Executes callback

        $this->assertSame(1, $incrementer, 'Callback execution went wrong');
    }

    public function testParametersArePassedToTheCallback()
    {
        $closure = function ($a, $b, $c) {
            $this->assertSame('hello world', $a, '1st parameter mismatch');
            $this->assertSame(['x', 'y'], $b, '2nd parameter mismatch');
            $this->assertSame(123, $c, '3rd parameter mismatch');
        };

        $ns = 'noFlash\FunctionsManipulator\Tests\Simulation_testClosureParam';
        $fn = 'testFunctionName';
        $accessor = $ns . '\\' . $fn;

        $injection = $this->getMockForAbstractClass(InjectableInterface::class);
        $injection
            ->expects($this->atLeastOnce())
            ->method('getFunctionName')
            ->willReturn($fn);
        $injection
            ->expects($this->atLeastOnce())
            ->method('getNamespace')
            ->willReturn($ns);
        $injection
            ->expects($this->any())
            ->method('getCallback')
            ->willReturn($closure);

        $this->subjectUnderTest->injectFunction($injection);
        $accessor('hello world', ['x', 'y'], 123); //Executes callback
    }

    public function testReturnValueIsPassedFromCallback()
    {
        $returnValue = mt_rand();

        $closure = function () use ($returnValue) {
            return $returnValue;
        };

        $ns = 'noFlash\FunctionsManipulator\Tests\Simulation_testClosureRet';
        $fn = 'testFunctionName';
        $accessor = $ns . '\\' . $fn;

        $injection = $this->getMockForAbstractClass(InjectableInterface::class);
        $injection
            ->expects($this->atLeastOnce())
            ->method('getFunctionName')
            ->willReturn($fn);
        $injection
            ->expects($this->atLeastOnce())
            ->method('getNamespace')
            ->willReturn($ns);
        $injection
            ->expects($this->any())
            ->method('getCallback')
            ->willReturn($closure);

        $this->subjectUnderTest->injectFunction($injection);
        $this->assertSame($returnValue, $accessor()); //Executes callback
    }

    public function testMultipleInjectionsToTheSameNamespaceArePossible()
    {
        $i1 = 100;
        $c1 = function () use (&$i1) { $i1++; };

        $i2 = 200;
        $c2 = function () use (&$i2) { $i2++; };

        $i3 = 300;
        $c3 = function () use (&$i3) { $i3++; };

        $ns = 'noFlash\FunctionsManipulator\Tests\Simulation_testClosureMulti';
        $fn1 = 'testFunctionName1';
        $fn2 = 'testFunctionName2';
        $fn3 = 'testFunctionName3';
        $accessor1 = $ns . '\\' . $fn1;
        $accessor2 = $ns . '\\' . $fn2;
        $accessor3 = $ns . '\\' . $fn3;

        $injection = $this->getMockForAbstractClass(InjectableInterface::class);
        $injection
            ->expects($this->atLeastOnce())
            ->method('getFunctionName')
            ->willReturn($fn1);
        $injection
            ->expects($this->atLeastOnce())
            ->method('getNamespace')
            ->willReturn($ns);
        $injection
            ->expects($this->any())
            ->method('getCallback')
            ->willReturn($c1);
        $this->subjectUnderTest->injectFunction($injection);

        $injection = $this->getMockForAbstractClass(InjectableInterface::class);
        $injection
            ->expects($this->atLeastOnce())
            ->method('getFunctionName')
            ->willReturn($fn2);
        $injection
            ->expects($this->atLeastOnce())
            ->method('getNamespace')
            ->willReturn($ns);
        $injection
            ->expects($this->any())
            ->method('getCallback')
            ->willReturn($c2);
        $this->subjectUnderTest->injectFunction($injection);

        $injection = $this->getMockForAbstractClass(InjectableInterface::class);
        $injection
            ->expects($this->atLeastOnce())
            ->method('getFunctionName')
            ->willReturn($fn3);
        $injection
            ->expects($this->atLeastOnce())
            ->method('getNamespace')
            ->willReturn($ns);
        $injection
            ->expects($this->any())
            ->method('getCallback')
            ->willReturn($c3);
        $this->subjectUnderTest->injectFunction($injection);

        $accessor1(); //Executes callback
        $accessor2(); //Executes callback
        $accessor3(); //Executes callback

        $this->assertSame(101, $i1, 'Callback #1 execution went wrong');
        $this->assertSame(201, $i2, 'Callback #2 execution went wrong');
        $this->assertSame(301, $i3, 'Callback #3 execution went wrong');
    }

    public function testNamespaceWithLeadingSlashWorksCorrectly()
    {
        $injection = $this->getMockForAbstractClass(InjectableInterface::class);
        $injection
            ->expects($this->atLeastOnce())
            ->method('getFunctionName')
            ->willReturn('testFunction');
        $injection
            ->expects($this->atLeastOnce())
            ->method('getNamespace')
            ->willReturn(
                '\noFlash\FunctionsManipulator\Tests\Simulation_testWithLeadingSlash'
            );

        //This will fail with parser exception if generated code is wrong
        $this->subjectUnderTest->injectFunction($injection);
    }

    public function testNamespaceWithoutLeadingSlashWorksCorrectly()
    {
        $injection = $this->getMockForAbstractClass(InjectableInterface::class);
        $injection
            ->expects($this->atLeastOnce())
            ->method('getFunctionName')
            ->willReturn('testFunction');
        $injection
            ->expects($this->atLeastOnce())
            ->method('getNamespace')
            ->willReturn(
                'noFlash\FunctionsManipulator\Tests\Simulation_testWithoutLeadingSlash'
            );

        //This will fail with parser exception if generated code is wrong
        $this->subjectUnderTest->injectFunction($injection);
    }

    public function testNamespaceWithTrailingSlashWorksCorrectly()
    {
        $injection = $this->getMockForAbstractClass(InjectableInterface::class);
        $injection
            ->expects($this->atLeastOnce())
            ->method('getFunctionName')
            ->willReturn('testFunction');
        $injection
            ->expects($this->atLeastOnce())
            ->method('getNamespace')
            ->willReturn(
                'noFlash\FunctionsManipulator\Tests\Simulation_testWithTrailingSlash\\'
            );

        //This will fail with parser exception if generated code is wrong
        $this->subjectUnderTest->injectFunction($injection);
    }

    public function testNamespaceWithoutTrailingSlashWorksCorrectly()
    {
        $injection = $this->getMockForAbstractClass(InjectableInterface::class);
        $injection
            ->expects($this->atLeastOnce())
            ->method('getFunctionName')
            ->willReturn('testFunction');
        $injection
            ->expects($this->atLeastOnce())
            ->method('getNamespace')
            ->willReturn(
                'noFlash\FunctionsManipulator\Tests\Simulation_testWithoutTrailingSlash'
            );

        //This will fail with parser exception if generated code is wrong
        $this->subjectUnderTest->injectFunction($injection);
    }

    public function testRootNamespaceWithLeadingSlashWorksCorrectly()
    {
        $injection = $this->getMockForAbstractClass(InjectableInterface::class);
        $injection
            ->expects($this->atLeastOnce())
            ->method('getFunctionName')
            ->willReturn('testFunctionRootWithLSL');
        $injection
            ->expects($this->atLeastOnce())
            ->method('getNamespace')
            ->willReturn('\\');

        //This will fail with parser exception if generated code is wrong
        $this->subjectUnderTest->injectFunction($injection);
    }

    public function testRootNamespaceWithoutLeadingSlashWorksCorrectly()
    {
        $injection = $this->getMockForAbstractClass(InjectableInterface::class);
        $injection
            ->expects($this->atLeastOnce())
            ->method('getFunctionName')
            ->willReturn('testFunctionRootWOLS');
        $injection
            ->expects($this->atLeastOnce())
            ->method('getNamespace')
            ->willReturn('');

        //This will fail with parser exception if generated code is wrong
        $this->subjectUnderTest->injectFunction($injection);
    }
}
