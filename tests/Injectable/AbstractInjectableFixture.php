<?php
/**
 * This file is part of FunctionInjector project.
 * You are using it at your own risk and you are fully responsible for everything that code will do.
 *
 * Copyright (c) 2017 Grzegorz Zdanowski <grzegorz@noflash.pl>
 *
 * For the full copyright and license information, please view the LICENSE file distributed with this source code.
 */

namespace noFlash\FunctionsManipulator\Tests\Injectable;

use noFlash\FunctionsManipulator\Injectable\AbstractInjectable;

class AbstractInjectableFixture extends AbstractInjectable
{
    public function getCallback()
    {
        throw new \LogicException(__METHOD__ . ' was not expected to be called');
    }
}
