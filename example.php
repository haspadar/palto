<?php

use Palto\Palto;
use Dotenv\Dotenv;

require_once 'vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$env = $dotenv->load();
$palto = new Palto('/region/level1/level2/level3/4/', $env);
echo $palto->getCategoryUrl();
echo $palto->getLayout();