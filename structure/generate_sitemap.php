<?php

use Palto\Palto;
use Palto\Sitemap;

require 'vendor/autoload.php';

$palto = new Palto();
$domainUrl = $palto->getEnv()['DOMAIN_URL'];
if (!$domainUrl) {
    $domainUrl = 'http://' . $palto->findDomainName();
}

$path = $palto->getEnv()['SITEMAP_PATH'];
if (!$path) {
    $path = '/sitemaps/' . $palto->findDomainName();
}

$sitemap = new Sitemap($domainUrl, $path, $palto);
$sitemap->generate();