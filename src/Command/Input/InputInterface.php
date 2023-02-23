<?php

namespace pjpawel\LightApi\Command\Input;

interface InputInterface
{

    public function getOption(string $name): int|string|null;

    public function getArgument(string $name): int|string|null;

}