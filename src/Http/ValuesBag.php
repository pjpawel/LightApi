<?php

namespace pjpawel\LightApi\Http;

class ValuesBag
{

    /**
     * @var array<string, mixed>
     */
    public array $parameters;

    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return string|int|null
     */
    public function get(string $key, mixed $default = null): string|int|null
    {
        return $this->parameters[$key] ?? $default;
    }
}