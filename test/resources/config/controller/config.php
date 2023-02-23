<?php

use pjpawel\LightApi\Test\resources\classes\Logger;

return [
    'projectDir' => realpath(__DIR__ . '/../../../../'),
    'services' => realpath(__DIR__ . '/../../classes/'),
    'trustedIPs' => [],
    'extensions' => [
    ],
    'container' => [
        Logger::class => []
    ],
];
