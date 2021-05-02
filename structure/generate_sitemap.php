<?php

use Palto\Palto;
use Palto\Sitemap;

require 'vendor/autoload.php';

$palto = new Palto();

$domainUrl = $palto->getEnv()['SITEMAP_DOMAIN_URL'];
$pathParts = explode('/', $palto->getRootDirectory());
if (!$domainUrl) {
    $domainUrl = 'http://' . $pathParts[count($pathParts) - 1];
}

$path = $palto->getEnv()['SITEMAP_PATH'];
if (!$path) {
    $path = '/sitemaps/' . $pathParts[count($pathParts) - 1];
}

$sitemap = new Sitemap($domainUrl, $path, $palto);
$sitemap->generate();