<?php

namespace pjpawel\LightApi\Container;

abstract class DefinedDefinition extends Definition
{

    abstract public function load(): object;

}