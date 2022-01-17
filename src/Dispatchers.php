<?php

namespace Palto;

use Palto\Dispatcher\Client;
use Palto\Dispatcher\Dispatcher;
use Palto\Router\Karman;
use Palto\Router\Router;

class Dispatchers
{
    public static function create(Router $router): Dispatcher
    {
        if ($router instanceof Karman) {
            return new \Palto\Dispatcher\Karman($router);
        } else {
            return new Client($router);
        }
    }
}