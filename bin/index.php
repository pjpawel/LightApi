<?php

require_once __DIR__ . '/../vendor/autoload.php';

use pjpawel\LightApi\Components\Runner\HttpRunner;
use pjpawel\LightApi\Env;
use pjpawel\LightApi\Kernel;


$runner = new HttpRunner(new Kernel(Env::getConfigFromEnv(__DIR__ . '/../config/')));
$runner->run();
