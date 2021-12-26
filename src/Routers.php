<?php

namespace Palto;

use Palto\Router\Ad;
use Palto\Router\Category;
use Palto\Router\Region;
use Palto\Router\Router;
use Palto\Router\Standard;

class Routers
{
    public static function create(Url $url): Router
    {
        $standardRouteLayout = Directory::getStandardRouteLayout($url->getPath());
        if ($standardRouteLayout) {
            $router = new Standard($url, $standardRouteLayout);
        } elseif ($url->isRegionPage()) {
            $router = new Region($url);
        } elseif ($url->isAdPage()) {
            $router = new Ad($url);
        } else {
            $router = new Category($url);
        }

        return $router;
    }
}