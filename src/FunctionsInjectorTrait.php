<?php


namespace noFlash\FunctionsManipulator;

use noFlash\FunctionsManipulator\Injectable\InjectableCallback;

trait FunctionsInjectorTrait
{
    /**
     * @var FunctionInjector
     */
    private $_injector;

    protected function injectIntoClassScope($fqcn, $function, callable $cb)
    {
        if (!class_exists($fqcn)) {
            throw new \RuntimeException(
                sprintf(
                    'Cannot inject "%s()" into "%s" scope - class does not exists',
                    $function,
                    $fqcn
                )
            );
        }

        $fqcn = trim($fqcn, '\\');
        $ns = substr($fqcn, 0, (int)strpos($fqcn, '\\'));

        $this->injectIntoNamespace($ns, $function, $cb);
    }

    protected function injectIntoNamespace($ns, $function, callable $cb)
    {
        if ($this->_injector === null) {
            $this->_injector = new FunctionInjector();
        }

        $injection = new InjectableCallback();
        $injection->setNamespace($ns);
        $injection->setFunctionName($function);
        $injection->setCallback($cb);

        $this->_injector->injectFunction($injection);
    }
}
