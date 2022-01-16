<?php

namespace Palto;

use Palto\Router\Ad;
use Palto\Router\Category;
use Palto\Router\Client;
use Palto\Router\Karman;
use Palto\Router\Region;
use Palto\Router\Router;
use Palto\Router\Standard;

class Routers
{
    public static function create(Url $url): Router
    {
        if ($url->isKarmanPage()) {
            $router = new Karman($url);
//        } elseif ($standardRouteLayout = Directory::getStaticLayout($url->getPath())) {
//            $router = new Standard($url, $standardRouteLayout);
//        } elseif ($url->isRegionPage()) {
//            $router = new Region($url);
//        } elseif ($url->isAdPage()) {
//            $router = new Ad($url);
//        } else {
//            $router = new Category($url);
        } else {
            $router = new Client($url);
        }

        return $router;
    }
}