<?php

namespace pjpawel\LightApi\Command\Input;

use Exception;
use pjpawel\LightApi\Exception\KernelException;

class Stdin implements InputInterface
{

    /**
     * @var Option[]
     */
    public array $options;
    /**
     * @var Argument[]
     */
    public array $arguments;

    public function loadArgsDefinitions(array $arguments, array $options): void
    {
        $this->arguments = $arguments;
        $this->options = $options;
    }

    /**
     * @return void
     * @throws KernelException
     */
    public function load(): void
    {
        $loadedArguments = [];
        $loadedOptions = [];
        $argv = $_SERVER['argv'];
        for ($i=1;$i <= count($argv); $i++) {
            if (!isset($argv[$i])) {
                continue;
            }
            if (str_contains($argv[$i], '=')) {
                [$optionName, $value] = explode('=', $argv, 2);
                if (str_starts_with($optionName, '--')) {
                    $loadedOptions[] = [
                        'shortName' => null,
                        'longName' => substr($optionName, 2),
                        'value' => $value
                    ];
                } elseif (str_starts_with($optionName, '-')) {
                    $loadedOptions[] = [
                        'shortName' => substr($optionName, 1),
                        'longName' => null,
                        'value' => $value
                    ];
                } else {
                    throw new KernelException('Uncovered option ' . $argv[$i]);
                }
            } elseif (str_starts_with($argv[$i], '--')) {
                $loadedOptions[] = [
                    'shortName' => null,
                    'longName' => substr($argv[$i], 2),
                    'value' => $argv[$i+1]
                ];
                unset($argv[$i+1]);
            } elseif (str_starts_with($argv[$i], '-')) {
                $loadedOptions[] = [
                    'shortName' => substr($argv[$i], 1),
                    'longName' => null,
                    'value' => $argv[$i+1]
                ];
                unset($argv[$i+1]);
            } else {
                $loadedArguments[] = $argv[$i];
            }
        }
        foreach ($this->arguments as $argument) {
            $argument->setValue(array_shift($loadedArguments));
            if ($argument->type == Argument::REQUIRED && $argument->value == null) {
                throw new Exception('Missing required argument: ' . $argument->name);
            }
        }
        foreach ($this->options as $option) {
            $loadedOption = $this->optionExists($loadedOptions, $option->shortcut, $option->name);
            if ($loadedOption !== false) {
                $option->value = $loadedOption['value'];
            }
            if ($option->type == Argument::REQUIRED && $option->value == null) {
                throw new Exception('Missing required option: ' . $option->name);
            }
        }
    }

    /**
     * @param array $options
     * @param string $shortName
     * @param string $longName
     * @return array|false
     */
    private function optionExists(array $options, string $shortName, string $longName): array|false
    {
        foreach ($options as $option) {
            if ($shortName == $option['shortName']) {
                return $option;
            }
            if ($longName == $option['longName']) {
                return $option;
            }
        }
        return false;
    }

    /**
     * @param string $name
     * @return int|string|null
     * @throws Exception
     */
    public function getOption(string $name): int|string|null
    {
        foreach ($this->options as $option) {
            if ($option->name === $name) {
                return $option->value ?? $option->default;
            }
        }
        throw new Exception("Option $name wasn't found");
    }

    /**
     * @param string $name
     * @return int|string|null
     * @throws Exception
     */
    public function getArgument(string $name): int|string|null
    {
        foreach ($this->arguments as $argument) {
            if ($argument->name === $name) {
                return $argument->value;
            }
        }
        throw new Exception("Argument $name wasn't found");
    }
}