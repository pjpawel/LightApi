<?php

return [
    'projectDir' => __DIR__ . '/../../../../',
    'trustedIPs' => [],
    'controllers' => [
        \pjpawel\LightApi\Test\resources\classes\ControllerOne::class
    ],
    'commands' => [],
    'components' => [
    ],
    'container' => [
        \pjpawel\LightApi\Test\resources\classes\Logger::class => []
    ],
];
