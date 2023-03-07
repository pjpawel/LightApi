<?php

namespace pjpawel\LightApi\Container\Definition;

abstract class DefinedDefinition extends Definition
{

    abstract public function load(): object;

}