<?php

use Bramus\Router\Router;
use Palto\Auth;
use Palto\Config;
use Palto\IP;
use Palto\Logger;
use Palto\Pages;
use Palto\Plates\Extension\Translate;

require_once '../vendor/autoload.php';

try {
    if (\Palto\Settings::isAuthEnabled()) {
        Auth::check();
    }

    $templatesEngine = new League\Plates\Engine(\Palto\Directory::getRootDirectory() . '/templates');
    $templatesEngine->loadExtension(new Translate());

    $router = new Router();
    $router->mount('/karman', function() use ($router) {
        $router->get('/', '\Palto\Controller\Karman@showKarmanIndex');
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
        $router->get('/get-categories', '\Palto\Controller\Karman@getCategoriesRoots');
        $router->get('/get-categories/{id}', '\Palto\Controller\Karman@getCategoriesChildren');
        $router->put('/move-ad', '\Palto\Controller\Karman@moveAd');
        $router->put('/update-category/{id}', '\Palto\Controller\Karman@updateCategory');
        $router->delete('/remove-category/{id}', '\Palto\Controller\Karman@removeCategory');
        $router->delete('/remove-emoji/{id}', '\Palto\Controller\Karman@removeEmoji');
        $router->get("/ads(/\d+)?", '\Palto\Controller\Karman@showAds');
        $router->get("/category-ads/{id}(/\d+)?", '\Palto\Controller\Karman@showCategoryAds');
        $router->get("/ad/{id}", '\Palto\Controller\Karman@showKarmanAd');
        $router->get("/find-ad-category/{id}", '\Palto\Controller\Karman@findAdCategory');

        $router->get("/info-logs-directories", '\Palto\Controller\Karman@showInfoLogsDirectories');
        $router->get("/error-logs-directories", '\Palto\Controller\Karman@showErrorLogsDirectories');

        $router->get("/info-logs/{name}", '\Palto\Controller\Karman@showInfoLogs');
        $router->get("/error-logs/{name}", '\Palto\Controller\Karman@showErrorLogs');
        $router->get("/get-logs/{name}/{type}", '\Palto\Controller\Karman@getLogs');

        $router->get("/settings", '\Palto\Controller\Karman@showSettings');
        $router->get("/settings/{id}", '\Palto\Controller\Karman@showSetting');
        $router->put('/update-setting/{id}', '\Palto\Controller\Karman@updateSetting');

        $router->get("/pages", '\Palto\Controller\Karman@showPages');
        $router->get("/pages/{id}", '\Palto\Controller\Karman@showPage');
        $router->put('/update-page/{id}', '\Palto\Controller\Karman@updatePage');

        $router->get("/templates", '\Palto\Controller\Karman@showTemplates');
        $router->get("/templates/{id}", '\Palto\Controller\Karman@showTemplate');

        $router->get("/translates", '\Palto\Controller\Karman@showTranslates');
        $router->get("/translates/{id}", '\Palto\Controller\Karman@showTranslate');
        $router->put('/update-translate/{id}', '\Palto\Controller\Karman@updateTranslate');
    });
    /**
     * @var \Palto\Page $page
     */
    foreach (Pages::getUniqueUrls() as $page) {
        if ($page->is404()) {
            $router->set404('\Palto\Controller\Client@' . $page->getFunction());
        } else {
            $router->get($page->getUrl(), '\Palto\Controller\Client@' . $page->getFunction());
        }
    }

    $router->run();

} catch (Exception $e) {
    Logger::error($e->getMessage());
    Logger::error($e->getTrace());
    throw $e;
}