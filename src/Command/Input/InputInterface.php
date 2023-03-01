<?php

namespace pjpawel\LightApi\Command\Input;

interface InputInterface
{

    public function addArgument(string $name, int $type = Argument::REQUIRED, string $description = null): void;

    public function addOption(string $name, ?string $shortcut = null, int $type = Option::OPTIONAL, null|string|int $default = null, string $description = null): void;

    public function getOption(string $name): int|string|null;

    public function getArgument(string $name): int|string|null;

}