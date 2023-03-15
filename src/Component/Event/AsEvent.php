<?php

namespace pjpawel\LightApi\Component\Event;

use Attribute;

#[Attribute(Attribute::TARGET_FUNCTION|Attribute::TARGET_METHOD)]
class AsEvent
{

    /**
     * @var string Event name
     */
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

}