<?php

require_once __DIR__ . '/../vendor/autoload.php';

use pjpawel\LightApi\Components\Runner\CliRunner;
use pjpawel\LightApi\Env;
use pjpawel\LightApi\Kernel;

$runner = new CliRunner(new Kernel(Env::getConfigFromEnv(__DIR__ . '/../config/')), $_SERVER['argc'][1]);
$runner->run();
return $runner->result;