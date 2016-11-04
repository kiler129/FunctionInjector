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

use noFlash\FunctionsManipulator\NameValidator;

class NameValidatorTest extends \PHPUnit_Framework_TestCase
{
    const RESERVED_KEYWORDS = [
        '__halt_compiler',
        'abstract',
        'and',
        'array',
        'as',
        'break',
        'callback',
        'case',
        'catch',
        'class',
        'clone',
        'const',
        'continue',
        'declare',
        'default',
        'die',
        'do',
        'echo',
        'else',
        'elseif',
        'empty',
        'enddeclare',
        'endfor',
        'endforeach',
        'endif',
        'endswitch',
        'endwhile',
        'eval',
        'exit',
        'extends',
        'final',
        'finally',
        'for',
        'foreach',
        'function',
        'global',
        'goto',
        'if',
        'implements',
        'include',
        'include_once',
        'instanceof',
        'insteadof',
        'interface',
        'isset',
        'list',
        'namespace',
        'new',
        'or',
        'print',
        'private',
        'protected',
        'public',
        'require',
        'require_once',
        'return',
        'static',
        'switch',
        'throw',
        'trait',
        'try',
        'use',
        'var',
        'while',
        'xor',
        'yield'
    ];

    /**
     * @var NameValidator
     */
    private $subjectUnderTest;

    public function setUp()
    {
        $this->subjectUnderTest = new NameValidator();
    }

    public function resultConstantsProvider()
    {
        return [
            ['RESULT_REGEX_FAILED'],
            ['RESULT_RESERVED_KEYWORD'],
            ['RESULT_OK'],
        ];
    }

    /**
     * @dataProvider resultConstantsProvider
     */
    public function testClassContainsResultConstants($name)
    {
        $this->assertTrue(
            defined(NameValidator::class . '::' . $name),
            "Constant $name not found in " . NameValidator::class
        );
    }

    public function namespacesWithReservedKeywordsProvider()
    {
        foreach (self::RESERVED_KEYWORDS as $keyword) {
            foreach (['%s', 'foo\\%s', 'foo\\%s\\bar', 'baz\\%s'] as $pattern) {
                yield [sprintf($pattern, $keyword)];
            }
        }
    }

    /**
     * @dataProvider namespacesWithReservedKeywordsProvider
     */
    public function testValidationFailsForNamespacesWithReservedKeywords($ns)
    {
        $this->assertSame(
            NameValidator::RESULT_RESERVED_KEYWORD,
            $this->subjectUnderTest->validateNamespace($ns)
        );
    }

    public function testValidationFailsForNamespaceContainingSpaces()
    {
        $this->assertSame(
            NameValidator::RESULT_REGEX_FAILED,
            $this->subjectUnderTest->validateNamespace('foo\\ bar')
        );
    }

    public function testValidationFailsForNamespaceStartingWithANumber()
    {
        $this->assertSame(
            NameValidator::RESULT_REGEX_FAILED,
            $this->subjectUnderTest->validateNamespace('69foo\\bar')
        );
    }

    public function testValidationFailsForNamespaceContainingDashes()
    {
        $this->assertSame(
            NameValidator::RESULT_REGEX_FAILED,
            $this->subjectUnderTest->validateNamespace('\\foo-bar')
        );
    }


    public function functionReservedNamesProvider()
    {
        foreach (self::RESERVED_KEYWORDS as $keyword) {
            yield [$keyword];
        }
    }

    /**
     * @dataProvider functionReservedNamesProvider
     */
    public function testValidationFailsForFunctionNameWithReservedKeywords($name)
    {
        $this->assertSame(
            NameValidator::RESULT_RESERVED_KEYWORD,
            $this->subjectUnderTest->validateFunctionName($name)
        );
    }

    public function testValidationFailsForFunctionNameContainingSpaces()
    {
        $this->assertSame(
            NameValidator::RESULT_REGEX_FAILED,
            $this->subjectUnderTest->validateFunctionName('foo bar')
        );
    }

    public function testValidationFailsForFunctionNameStartingWithANumber()
    {
        $this->assertSame(
            NameValidator::RESULT_REGEX_FAILED,
            $this->subjectUnderTest->validateFunctionName('42fooBar')
        );
    }

    public function testValidationFailsForFunctionNameContainingDashes()
    {
        $this->assertSame(
            NameValidator::RESULT_REGEX_FAILED,
            $this->subjectUnderTest->validateFunctionName('foo-bar')
        );
    }

    /**
     * @testdox Class provides getErrorFromCode() function
     */
    public function testClassProvidesStaticGetErrorFromCodeFunction()
    {
        $reflection = new \ReflectionClass(NameValidator::class);

        $this->assertTrue(
            $reflection->hasMethod('getErrorFromCode'),
            'No method named "getErrorFromCode" found'
        );
        $this->assertTrue(
            $reflection->getMethod('getErrorFromCode')->isStatic(),
            'Method "getErrorFromCode" is not static'
        );
    }

    public function errorCodesErrorsProvider()
    {
        return [
            [NameValidator::RESULT_OK, 'no error'],
            [NameValidator::RESULT_RESERVED_KEYWORD, 'reserved keyword'],
            [NameValidator::RESULT_REGEX_FAILED, 'regex'],
            [123, 'unknown'],
            [321, 'unknown']
        ];
    }

    /**
     * @dataProvider errorCodesErrorsProvider
     */
    public function testErrorCodeDescriptionContainsNessescaryInformation($code, $info)
    {
        $this->assertContains($info, NameValidator::getErrorFromCode($code));
    }

    public function testTryingToGetErrorCodeDescriptionWhilePassingNonIntegerValueThrowsException()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        NameValidator::getErrorFromCode(M_PI);
    }
}
