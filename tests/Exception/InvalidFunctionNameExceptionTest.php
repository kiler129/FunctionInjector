<?php


namespace noFlash\FunctionsManipulator\Tests\Exception;

use noFlash\FunctionsManipulator\Exception\InvalidFunctionNameException;
use noFlash\FunctionsManipulator\NameValidator;

class InvalidFunctionNameExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsAnException()
    {
        $this->assertTrue(
            is_subclass_of(InvalidFunctionNameException::class, \Exception::class)
        );
    }

    public function testGeneratedMessageContainsFunctionName()
    {
        $functionName = sha1(microtime(true) . mt_rand());
        $sut = new InvalidFunctionNameException($functionName, 0);

        $this->assertContains($functionName, $sut->getMessage());
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
        $sut = new InvalidFunctionNameException($msg, $code);
        $this->assertContains($msg, $sut->getMessage());
    }
}
