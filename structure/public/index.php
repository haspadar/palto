<?php

use Palto\Palto;

$rootDirectory = require_once 'autoload.php';

$palto = new Palto($rootDirectory);
if ($palto->getEnv()['AUTH'] && $palto->getIP() != '127.0.0.1') {
    $palto->checkAuth();
}

$palto->loadLayout($palto->getLayout());
