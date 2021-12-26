<?php

use Palto\Dispatcher;
use Palto\Routers;
use Palto\Url;

require_once 'autoload.php';

$url = new Url();
$router = Routers::create($url);
$dispatcher = new Dispatcher($router);
$dispatcher->run();
