<?php

use Bramus\Router\Router;
use Palto\Auth;
use Palto\Config;
use Palto\IP;
use Palto\Plates\Extension\Translate;
use Palto\Strategy;

require_once '../vendor/autoload.php';

if (Config::get('AUTH') && !IP::isLocal()) {
    Auth::check();
}

$templatesEngine = new League\Plates\Engine(\Palto\Directory::getRootDirectory() . '/templates');
$templatesEngine->loadExtension(new Translate());

$router = new Router();
$router->mount('/karman', function() use ($router) {
    $router->get('/', '\Palto\Controller\Karman@showComplaints');
    $router->get('/complaints/{id}', '\Palto\Controller\Karman@showComplaint');
    $router->get('/complaints', '\Palto\Controller\Karman@showComplaints');
    $router->put('/ignore-complaint/{id}', '\Palto\Controller\Karman@ignoreComplaint');
    $router->put('/ignore-complaints', '\Palto\Controller\Karman@ignoreComplaints');
    $router->delete('/remove-ad/{id}', '\Palto\Controller\Karman@removeAd');
    $router->delete('/remove-ads', '\Palto\Controller\Karman@removeAds');
    $router->get('/status', '\Palto\Controller\Karman@showStatus');
    $router->put('/disable-site', '\Palto\Controller\Karman@disableSite');
    $router->put('/enable-site', '\Palto\Controller\Karman@enableSite');
    $router->put('/disable-cache', '\Palto\Controller\Karman@disableCache');
    $router->put('/enable-cache', '\Palto\Controller\Karman@enableCache');
    $router->get('/categories/{id}', '\Palto\Controller\Karman@showCategory');
    $router->get('/categories', '\Palto\Controller\Karman@showCategories');
    $router->put('/update-category/{id}', '\Palto\Controller\Karman@updateCategory');
    $router->delete('/remove-emoji/{id}', '\Palto\Controller\Karman@removeEmoji');
});

$controller = Strategy::isSingleCategory() ? 'ClientRegions' : 'ClientCategories';
$router->get('/', "\Palto\Controller\\$controller@showIndex");
$router->get('/registration', "\Palto\Controller\\$controller@showRegistration");
$router->get('/regions', "\Palto\Controller\\$controller@showRegionsList");
$word = "[a-zA-Z0-9_-]";

if (Strategy::isSingleCategory()) {
//    /region
    $router->get("/($word+)(/$word+)?/ad(\d+)", "\Palto\Controller\\$controller@showAd");
    $router->get("/($word+)(/\d+)?", "\Palto\Controller\\$controller@showRegion");
} else {
    $router->get('/categories', "\Palto\Controller\\$controller@showCategoriesList");
//    /region/category1/category2/category3
    $router->get("/($word+)/($word+)(/$word+)?(/$word+)?/ad(\d+)", "\Palto\Controller\\$controller@showAd");
    $router->get("/($word+)(/\d+)?", "\Palto\Controller\\$controller@showRegion");
    $router->get("/($word+)/($word+)(/$word+)?(/$word+)?(/\d+)?", "\Palto\Controller\\$controller@showCategory");
}

$router->set404("\Palto\Controller\\$controller@showNotFound");
$router->run();
