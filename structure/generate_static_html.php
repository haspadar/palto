<?php

use Palto\Palto;
use Palto\StaticHtml;

require 'vendor/autoload.php';

$palto = new Palto();
$staticHtml = new StaticHtml($palto->getPhpDomainUrl(), '/static', $palto);
$staticHtml->generate();