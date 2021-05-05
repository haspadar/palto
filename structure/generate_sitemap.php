<?php

use Palto\Palto;
use Palto\Sitemap;

require 'vendor/autoload.php';

$palto = new Palto();
$domainUrl = $palto->getEnv()['DOMAIN_URL'];
if (!$domainUrl) {
    $domainUrl = 'http://' . $palto->getProjectName();
}

$path = $palto->getEnv()['SITEMAP_PATH'];
if (!$path) {
    $path = '/sitemaps/' . $palto->getProjectName();
}

$sitemap = new Sitemap($domainUrl, $path, $palto);
$sitemap->generate();