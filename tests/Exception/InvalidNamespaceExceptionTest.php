<?php


namespace noFlash\FunctionsManipulator\Tests\Exception;

use noFlash\FunctionsManipulator\Exception\InvalidNamespaceException;
use noFlash\FunctionsManipulator\NameValidator;

class InvalidNamespaceExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsAnException()
    {
        $this->assertTrue(
            is_subclass_of(InvalidNamespaceException::class, \Exception::class)
        );
    }

    public function testGeneratedMessageContainsNamespaceName()
    {
        $namespaceName = sha1(microtime(true) . mt_rand());
        $sut = new InvalidNamespaceException($namespaceName, 0);

        $this->assertContains($namespaceName, $sut->getMessage());
    }

    public function errorCodesProvider()
    {
        return [
            [NameValidator::RESULT_OK, NameValidator::getErrorFromCode(NameValidator::RESULT_OK)],
            [NameValidator::RESULT_RESERVED_KEYWORD, NameValidator::getErrorFromCode(NameValidator::RESULT_RESERVED_KEYWORD)],
            [NameValidator::RESULT_REGEX_FAILED, NameValidator::getErrorFromCode(NameValidator::RESULT_REGEX_FAILED)]
        ];
    }

    /**
     * @dataProvider errorCodesProvider
     */
    public function testGeneratedMessageContainsReasonCodeText($code, $msg)
    {
        $sut = new InvalidNamespaceException($msg, $code);
        $this->assertContains($msg, $sut->getMessage());
    }
}
