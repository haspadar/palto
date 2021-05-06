<?php

use Palto\Palto;
use Palto\Sitemap;

require 'vendor/autoload.php';

$palto = new Palto();
$path = $palto->getEnv()['SITEMAP_PATH'];
if (!$path) {
    $path = '/sitemaps/' . $palto->getProjectName();
}

$sitemap = new Sitemap($palto->getDomainUrl(), $path, $palto);
$sitemap->generate();