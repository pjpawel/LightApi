<?php

require_once __DIR__ . '/../vendor/autoload.php';

use pjpawel\LightApi\Debug\KernelDebugger;
use pjpawel\LightApi\Env;
use pjpawel\LightApi\Kernel;


$kernel = new Kernel(Env::getConfigFromEnv(__DIR__ . '/../config/'));

$debug = new KernelDebugger($kernel);

$debug->show();

