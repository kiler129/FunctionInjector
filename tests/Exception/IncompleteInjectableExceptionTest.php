<?php


namespace noFlash\FunctionsManipulator\Tests\Exception;

use noFlash\FunctionsManipulator\Exception\IncompleteInjectableException;

class IncompleteInjectableExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsAnException()
    {
        $this->assertTrue(
            is_subclass_of(IncompleteInjectableException::class, \Exception::class)
        );
    }
}
