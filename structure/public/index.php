<?php

use Palto\Palto;

$rootDirectory = require_once 'autoload.php';

$palto = new Palto($rootDirectory);
if ($palto->getEnv()['AUTH'] && $palto->getIP() != '127.0.0.1') {
    $palto->checkAuth();
    ini_set('display_errors', true);
    ini_set('display_startup_errors', true);
} elseif ($palto->isDebug()) {
    ini_set('display_errors', true);
    ini_set('display_startup_errors', true);
}

$palto->loadLayout($palto->getLayout());
