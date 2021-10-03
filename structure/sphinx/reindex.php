<?php

use Palto\Palto;
use Palto\Search;

require '../vendor/autoload.php';

$palto = new Palto('../');
Search::reIndex($palto->getLogger());