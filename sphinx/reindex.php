<?php

use Palto\ExecutionTime;
use Palto\Palto;
use Palto\Sphinx;

require '../vendor/autoload.php';

$palto = new Palto('../');
$sphinx = new Sphinx();
$executionTime = new ExecutionTime();
$executionTime->start();
$commandOutput = $sphinx->reIndex();
$palto->getLogger()->info($commandOutput);
$executionTime->end();
$palto->getLogger()->info('Sphinx reIndexed for ' . $executionTime->get());