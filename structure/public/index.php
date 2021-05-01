<?php

use Palto\Palto;

require_once '../vendor/autoload.php';

$palto = new Palto();
if ($palto->getEnv()['AUTH']) {
    $palto->checkAuth();
}

$palto->loadLayout($palto->getLayout());
