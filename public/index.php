<?php

use Bramus\Router\Router;
use Palto\Plates\Extension\Translate;

require_once '../vendor/autoload.php';

$templatesEngine = new League\Plates\Engine(\Palto\Directory::getRootDirectory() . '/templates');
$templatesEngine->loadExtension(new Translate());

$router = new Router();
$router->mount('/karman', function() use ($router) {
    \Palto\Auth::check();
    $router->get('/', '\Palto\Controller\Karman@showComplaints');
    $router->get('/complaints', '\Palto\Controller\Karman@showComplaints');
});

$router->get('/', '\Palto\Controller\Client@showIndex');
$router->get('/registration', '\Palto\Controller\Client@showRegistration');
$router->get('/regions', '\Palto\Controller\Client@showRegionsList');
$router->get('/categories', '\Palto\Controller\Client@showCategoriesList');
$word = "[a-zA-Z0-9_-]";
$router->get("/($word+)/($word+)(/$word+)?(/$word+)?/ad(\d+)", '\Palto\Controller\Client@showAd');
$router->get("/($word+)(/\d+)?", '\Palto\Controller\Client@showRegion');
$router->get("/($word+)/($word+)(/$word+)?(/$word+)?(/\d+)?", '\Palto\Controller\Client@showCategory');
$router->set404('\Palto\Controller\Client@showNotFound');
$router->run();
