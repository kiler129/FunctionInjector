<?php


namespace noFlash\FunctionsManipulator\Injectable;

interface InjectableInterface
{
    public function getNamespace();

    public function getFunctionName();

    public function getCallback();
}
