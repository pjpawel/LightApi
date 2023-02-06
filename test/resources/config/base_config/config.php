<?php

return [
    'projectDir' => realpath(__DIR__ . '/../../../../'),
    'trustedIPs' => [],
    'controllers' => [],
    'commands' => [],
    'components' => [
    ],
    'container' => [
        \pjpawel\LightApi\Test\resources\classes\Logger::class => []
    ],
];
