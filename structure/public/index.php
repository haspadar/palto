<?php

use Palto\Dispatcher;
use Palto\Routers;
use Palto\Url;

require_once 'autoload.php';
var_dump(\Palto\Directory::getRootDirectory());exit;
$url = new Url();
$router = Routers::create($url);
$dispatcher = new Dispatcher($router);
$dispatcher->run();
