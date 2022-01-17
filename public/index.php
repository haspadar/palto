<?php

use Palto\Dispatchers;
use Palto\Routers;
use Palto\Url;

require_once '../vendor/autoload.php';

$url = new Url();
$router = Routers::create($url);
$dispatcher = Dispatchers::create($router);
$dispatcher->run();