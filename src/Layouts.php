<?php

namespace Palto;

use Palto\Dispatcher\Client;
use Palto\Dispatcher\Dispatcher;

class Layouts
{
    public static function create(Dispatcher $dispatcher): \Palto\Layout\Layout
    {
        if ($dispatcher instanceof Client) {
            return new Layout\Client($dispatcher);
        } else {
            return new Layout\Karman($dispatcher);
        }
    }
}