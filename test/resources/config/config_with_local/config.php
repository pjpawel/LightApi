<?php

use pjpawel\LightApi\Test\resources\classes\Logger;

return [
    'services' => realpath(__DIR__ . '/../../classes/'),
    'trustedIPs' => [],
    'container' => [
        Logger::class => []
    ],
    'extensions' => [
    ]
];
