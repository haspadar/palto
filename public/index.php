<?php

use Bramus\Router\Router;
use Palto\Debug;
use Palto\Plates\Extension\Translate;

require_once '../vendor/autoload.php';

$templatesEngine = new League\Plates\Engine(\Palto\Directory::getRootDirectory() . '/templates');
$templatesEngine->loadExtension(new Translate());


$router = new Router();

//$router->setNamespace('\Palto\Controller');


$parsed = new stdClass();
// Define routes
$router->get('/', '\Palto\Controller\Client@showIndex');
//$router->get('/', function() use ($templatesEngine, $parsed) {
//    $parsed->template = $templatesEngine->make('index');
//    $templatesEngine->addData(['name' => 'Jonathan']);
//    echo 'Index Page Contents';
//});
$word = "[a-zA-Z0-9_-]";
$router->get("/($word+)/($word+)(/$word+)?(/$word+)?/ad(\d+)", '\Palto\Controller\Client@showAd');
//$router->get('/(\w+)/(\w+)(/\w+)?(/\w+)?/ad(\d+)', function($region, $categoryLevel1, $categoryLevel2 = null, $categoryLevel3 = null, $adId = null) {
//    Debug::dump($region);
//    Debug::dump($categoryLevel1);
//    Debug::dump($categoryLevel2);
//    Debug::dump($categoryLevel3);
//    Debug::dump($adId);
//    echo 'Ad Page Contents';
//});


$router->get("/($word+)(/\d+)?", '\Palto\Controller\Client@showRegion');
//$router->get('/(\w+)(/\d+)?', function($region, $pageNumber) use ($templatesEngine, $parsed) {
//    $parsed->template = $templatesEngine->make('list');
//    $templatesEngine->addData(['page' => 'Jonathan']);
//    Debug::dump($pageNumber);
//    echo 'Region Page Contents';
//});
$router->get("/($word+)/($word+)(/$word+)?(/$word+)?(/\d+)?", '\Palto\Controller\Client@showCategory');
//$router->get('/(\w+)/(\w+)(/\w+)?(/\w+)?(/\d+)?', function($region, $categoryLevel1, $categoryLevel2 = null, $categoryLevel3 = null, $pageNumber = null) {
//    Debug::dump($region);
//    Debug::dump($categoryLevel1);
//    Debug::dump($categoryLevel2);
//    Debug::dump($categoryLevel3);
//    Debug::dump($pageNumber);
//    echo 'Category Page Contents';
//});


$router->set404('\Palto\Controller\Client@showNotFound');
//$router->set404(function() {
//    header('HTTP/1.1 404 Not Found');
//    echo 'Hui';
//});

// Run it!
$router->run(function() use ($parsed) {
//    echo $parsed->template;
});


//$url = new Url();
//$router = Routers::create($url);
//$dispatcher = Dispatchers::create($router);
//$dispatcher->run();