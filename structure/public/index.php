<?php

use Palto\Palto;

$rootDirectory = require_once 'autoload.php';

require_once $rootDirectory . '/vendor/autoload.php';
$palto = new Palto($rootDirectory);
if ($palto->getEnv()['AUTH']) {
    $palto->checkAuth();
}

$palto->loadLayout($palto->getLayout());
