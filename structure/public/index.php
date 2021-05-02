<?php

use Palto\Palto;

if (file_exists('../vendor/autoload.php')) {
    $rootDirectory = '..';
} else {
    $rootDirectory = '../..';
}

require_once $rootDirectory . '/vendor/autoload.php';
$palto = new Palto($rootDirectory);
if ($palto->getEnv()['AUTH']) {
    $palto->checkAuth();
}

$palto->loadLayout($palto->getLayout());
