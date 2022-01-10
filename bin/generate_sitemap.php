#!/usr/bin/php
<?php

use Palto\Config;
use Palto\Sitemap;

require_once 'safe_require_composer.php';

$path = Config::get('SITEMAP_PATH');
if (!$path) {
    $path = '/sitemaps/' . \Palto\Directory::getProjectName();
}

$sitemap = new Sitemap(Config::getDomainUrl(), $path);
$sitemap->generate();