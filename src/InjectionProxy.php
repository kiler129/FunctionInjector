<?php


namespace noFlash\FunctionsManipulator;


use noFlash\FunctionsManipulator\Exception\ScopeNotFoundException;
use noFlash\FunctionsManipulator\Injectable\InjectableInterface;

final class InjectionProxy
{
    /**
     * @var InjectableInterface[]
     */
    private static $injectables = [];

    private function __construct()
    {
    }

    public static function setInjection($scopeId, InjectableInterface $injectable)
    {
        self::$injectables[$scopeId] = $injectable;
    }

    public static function hasInjection($scopeId)
    {
        return isset(self::$injectables[$scopeId]);
    }

    public static function getInjection($scopeId)
    {
        if (!self::hasInjection($scopeId)) {
            throw new ScopeNotFoundException($scopeId);
        }

        return self::$injectables[$scopeId];
    }

    public static function callInjection($scopeId, array $args)
    {
        if (!self::hasInjection($scopeId)) {
            throw new ScopeNotFoundException($scopeId);
        }

        $cb = self::$injectables[$scopeId]->getCallback();
        return $cb(...$args);
    }
}
