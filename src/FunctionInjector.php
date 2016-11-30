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
use noFlash\FunctionsManipulator\Exception\InjectionCollisionException;
use noFlash\FunctionsManipulator\Exception\InjectionMismatchException;
use noFlash\FunctionsManipulator\Exception\RedeclareException;
use noFlash\FunctionsManipulator\Exception\ScopeGenerationLimitException;
use noFlash\FunctionsManipulator\Exception\ScopeNotFoundException;
use noFlash\FunctionsManipulator\Injectable\InjectableInterface;

class FunctionInjector
{
    /**
     * sprintf()-complaint pattern for scope generation
     *
     * @var string Uses 2 parameters - NS and FunctionName
     * @internal
     */
    const FAKE_SCOPE_PATTERN = '_noFlash_FunctionsManipulator_fakeScope_%s_%s';

    protected function getScopeKey($ns, $functionName)
    {
        return sprintf(self::FAKE_SCOPE_PATTERN, strtr($ns, ['\\' => '']), $functionName);
    }

    private function getInjectionCode($scopeId, InjectableInterface $injection)
    {
        $ns = trim($injection->getNamespace(), '\\');

        $injectionCode = <<<CODE
namespace $ns {
    function {$injection->getFunctionName()}(...\$args) {
        \$cb = \$GLOBALS['$scopeId']->getCallback();
        return \$cb(...\$args);
    }
}
CODE;

        return $injectionCode;
    }

    private function getAccessor($ns, $functionName)
    {
        $ns = trim($ns, '\\');
        return '\\' . ((empty($ns)) ? $functionName : ($ns . '\\' . $functionName));
    }

    /**
     * Injects given injectable entity. If injection already exists method will fail.
     *
     * @param InjectableInterface $injection
     *
     * @return string Scope ID to use later with replaceFunctionInjection()
     *
     * @throws RedeclareException Injection/function already exists
     */
    public function injectFunction(InjectableInterface $injection)
    {
        $functionName = $injection->getFunctionName();
        if ($functionName === null) {
            throw new IncompleteInjectableException('Passed injectable lacks function name');
        }

        $ns = $injection->getNamespace();
        $scopeId = $this->getScopeKey($ns, $functionName);

        if (isset($GLOBALS[$scopeId])) { //There is already an injection - just stop here
            throw new InjectionCollisionException($ns, $functionName);
        }

        $this->injectProxyCode($scopeId, $injection);

        $GLOBALS[$scopeId] = $injection;

        return $scopeId;
    }

    private function injectProxyCode($scopeId, InjectableInterface $injection)
    {
        $ns = $injection->getNamespace();
        $functionName = $injection->getFunctionName();

        if (function_exists($this->getAccessor($ns, $functionName))) {
            throw new RedeclareException($ns, $functionName);
        }

        //The only valid use-case for eval() in my career...
        eval($this->getInjectionCode($scopeId, $injection));
    }

    /**
     * Injects given injectable entity. If injection already exists it will be replaced
     *
     * @param InjectableInterface $injection
     *
     * @return string Scope ID to use later with replaceFunctionInjection()
     *
     * @throws RedeclareException Function, which is not injection, already exists
     */
    public function forceInjectFunction(InjectableInterface $injection)
    {
        $functionName = $injection->getFunctionName();
        if ($functionName === null) {
            throw new IncompleteInjectableException('Passed injectable lacks function name');
        }

        $ns = $injection->getNamespace();
        $scopeId = $this->getScopeKey($ns, $functionName);

        if (!isset($GLOBALS[$scopeId])) { //New injection - we need to inject code
            $this->injectProxyCode($scopeId, $injection);
        }

        $GLOBALS[$scopeId] = $injection;

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
