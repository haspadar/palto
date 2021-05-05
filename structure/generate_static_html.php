<?php

use Palto\Palto;
use Palto\StaticHtml;

require 'vendor/autoload.php';

$palto = new Palto();
$pathParts = explode('/', $palto->getRootDirectory());
$domainUrl = $palto->getEnv()['PHP_DOMAIN_URL'];
if (!$domainUrl) {
    $domainUrl = 'http://php.' . $palto->findDomainName();
}

$staticHtml = new StaticHtml($domainUrl, '/static', $palto);
$staticHtml->generate();