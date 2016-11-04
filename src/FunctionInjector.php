<?php
/**
 * This file is part of FunctionInjector project.
 * You are using it at your own risk and you are fully responsible for everything that code will do.
 *
 * Copyright (c) 2016 Grzegorz Zdanowski <grzegorz@noflash.pl>
 *
 * For the full copyright and license information, please view the LICENSE file distributed with this source code.
 */

namespace noFlash\FunctionsManipulator;

use noFlash\FunctionsManipulator\Exception\IncompleteInjectableException;
use noFlash\FunctionsManipulator\Exception\InjectionMismatchException;
use noFlash\FunctionsManipulator\Exception\ScopeGenerationLimitException;
use noFlash\FunctionsManipulator\Exception\ScopeNotFoundException;
use noFlash\FunctionsManipulator\Injectable\InjectableInterface;

class FunctionInjector
{
    /**
     * @var string sprintf()-complaint pattern for scope generation
     * @internal
     */
    const FAKE_SCOPE_PATTERN = '_noFlash_FunctionsManipulator_fakeScope_%s';

    /**
     * @var string How many times script should try to find unique scope key.
     * @internal
     */
    const SCOPE_KEY_MAX_LOOPS = 100;

    protected function getUniqueScopeKey()
    {
        for ($i = 0; $i < static::SCOPE_KEY_MAX_LOOPS; $i++) {
            $key = sprintf(static::FAKE_SCOPE_PATTERN, sha1(uniqid()));

            if (!isset($GLOBALS[$key])) {
                return $key;
            }
        }

        throw new ScopeGenerationLimitException(static::SCOPE_KEY_MAX_LOOPS);
    }

    private function getInjectionCode($scopeId, InjectableInterface $injection)
    {
        $ns = trim($injection->getNamespace(), '\\');

        $injectionCode = <<<CODE
namespace $ns {
    function {$injection->getFunctionName()}() {
        return call_user_func_array(
                    \$GLOBALS['$scopeId']->getCallback(), 
                    func_get_args()
               ); 
    }
}
CODE;

        return $injectionCode;
    }

    public function injectFunction(InjectableInterface $injection)
    {
        if ($injection->getFunctionName() === null) {
            throw new IncompleteInjectableException(
                'Passed injectable lacks function name'
            );
        }

        $scopeId = $this->getUniqueScopeKey();
        $GLOBALS[$scopeId] = $injection;
        $injectionCode = $this->getInjectionCode($scopeId, $injection);

        //The only valid use-case for eval() in my career...
        eval($injectionCode);

        return $scopeId;
    }

    public function replaceFunctionInjection($scopeId, InjectableInterface $inj)
    {
        if (!isset($GLOBALS[$scopeId]) ||
            !($GLOBALS[$scopeId] instanceof InjectableInterface)
        ) {
            throw new ScopeNotFoundException($scopeId);
        }

        /** @var InjectableInterface $oldInjection */
        $oldInjection = $GLOBALS[$scopeId];

        $oldNS = $oldInjection->getNamespace();
        $newNS = $inj->getNamespace();
        if ($newNS !== null && $oldNS !== $newNS) {
            throw new InjectionMismatchException('namespace', $oldNS, $newNS);
        }

        $oldFN = $oldInjection->getFunctionName();
        $newFN = $inj->getFunctionName();
        if ($newNS !== null && $oldNS !== $newNS) {
            throw new InjectionMismatchException('function name', $oldFN, $newFN);
        }

        $GLOBALS[$scopeId] = $inj;
    }
}
