<?php


namespace noFlash\FunctionsManipulator\Tests\Exception;

use noFlash\FunctionsManipulator\Exception\ScopeNotFoundException;

class ScopeNotFoundExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsAnException()
    {
        $this->assertTrue(
            is_subclass_of(ScopeNotFoundException::class, \Exception::class)
        );
    }

    public function testGeneratedMessageContainsScopeId()
    {
        $scopeId = sha1(uniqid());
        $expected = "ID=$scopeId";

        $sut = new ScopeNotFoundException($scopeId);
        $this->assertContains($expected, $sut->getMessage());
    }
}
