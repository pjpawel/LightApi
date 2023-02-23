<?php

use pjpawel\LightApi\Test\resources\classes\Logger;

return [
    'projectDir' => realpath(__DIR__ . '/../../../../'),
    'trustedIPs' => [],
    'extensions' => [
    ],
    'container' => [
        Logger::class => []
    ],
    'services' => realpath(__DIR__ . '/../../classes/')
];
