<?php

use pjpawel\LightApi\Test\resources\classes\Logger;

return [
    'projectDir' => realpath(__DIR__ . '/../../../../'),
    'trustedIPs' => [],
    'controllers' => [],
    'commands' => [],
    'components' => [
    ],
    'container' => [
        Logger::class => []
    ],
];
