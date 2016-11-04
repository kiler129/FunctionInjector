<?php


namespace noFlash\FunctionsManipulator\Tests\Exception;

use noFlash\FunctionsManipulator\Exception\InjectionMismatchException;

class InjectionMismatchExceptionTest extends \PHPUnit_Framework_TestCase
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
