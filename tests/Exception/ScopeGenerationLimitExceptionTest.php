<?php


namespace noFlash\FunctionsManipulator\Tests\Exception;

use noFlash\FunctionsManipulator\Exception\ScopeGenerationLimitException;

class ScopeGenerationLimitExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsAnException()
    {
        $this->assertTrue(
            is_subclass_of(ScopeGenerationLimitException::class, \Exception::class)
        );
    }

    public function testGeneratedMessageContainsNumberOfAttempts()
    {
        $limit = mt_rand();
        $expected = "$limit attempts";

        $sut = new ScopeGenerationLimitException($limit);
        $this->assertContains($expected, $sut->getMessage());
    }
}
