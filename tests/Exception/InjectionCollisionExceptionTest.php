<?php


namespace noFlash\FunctionsManipulator\Tests\Exception;


use noFlash\FunctionsManipulator\Exception\InjectionCollisionException;
use PHPUnit\Framework\TestCase;


class InjectionCollisionExceptionTest extends TestCase
{
    public function testClassIsAnException()
    {
        $this->assertTrue(
            is_subclass_of(InjectionCollisionException::class, \Exception::class)
        );
    }

    public function testGeneratedMessageContainsNamespace()
    {
        $ns = sha1(microtime(true) . mt_rand());
        $sut = new InjectionCollisionException($ns, null);

        $this->assertContains($ns, $sut->getMessage());
    }

    public function testGeneratedMessageContainsGlobalNamespaceNoteIfNamespaceWasEmpty()
    {
        $sut = new InjectionCollisionException('', null);

        $this->assertContains('global namespace', $sut->getMessage());
    }

    public function testGeneratedMessageContainsFunctionName()
    {
        $functionName = sha1(microtime(true) . mt_rand());
        $sut = new InjectionCollisionException(null, $functionName);

        $this->assertContains($functionName, $sut->getMessage());
    }

    public function testGeneratedMessageContainsInformationAboutCollisionReason()
    {
        $sut = new InjectionCollisionException(null, null);

        $this->assertContains('there is another injection in this scope', $sut->getMessage());
    }
}
