<?php

use Palto\IP;
use Palto\Palto;

$rootDirectory = require_once 'autoload.php';

$palto = new Palto($rootDirectory);
if ($palto->getEnv()['AUTH'] && !IP::isLocal()) {
    $palto->checkAuth();
    ini_set('display_errors', true);
    ini_set('display_startup_errors', true);
} elseif ($palto->isDebug()) {
    ini_set('display_errors', true);
    ini_set('display_startup_errors', true);
    $palto->getDb()->query('SET SESSION query_cache_type=0;');
}

$palto->loadLayout();