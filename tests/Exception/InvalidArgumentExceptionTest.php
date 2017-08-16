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

namespace noFlash\FunctionsManipulator\Tests\Exception;

use noFlash\FunctionsManipulator\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class InvalidArgumentExceptionTest extends TestCase
{
    /**
     * @testdox Class extends \InvalidArgumentException
     */
    public function testClassExtendsInvalidArgumentException()
    {
        $this->assertTrue(
            is_subclass_of(
                InvalidArgumentException::class,
                \InvalidArgumentException::class
            )
        );
    }
}
