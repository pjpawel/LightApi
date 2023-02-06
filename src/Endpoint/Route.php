<?php

namespace pjpawel\LightApi\Endpoint;

use Attribute;

/**
 * Class is to be used as attribute to expose methods as routes to kernel
 */
#[Attribute(Attribute::TARGET_FUNCTION|Attribute::TARGET_METHOD)]
class Route
{

    public string $path;
    /**
     * @var string[]
     */
    public array $methods;
    //public array $requirements;

    public function __construct($path, $methods = [])
    {
        $this->path = $path;
        $this->methods = $methods;
    }

}