<?php

use Palto\Sphinx;

require_once 'vendor/autoload.php';

$sphinx = new Sphinx();
$sphinx->install('/var/www/');