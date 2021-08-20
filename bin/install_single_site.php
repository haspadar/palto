#!/usr/bin/php
<?php

use Palto\Install;
use Palto\Palto;

require_once 'vendor/autoload.php';

$install = new Install();
$install->run(Palto::PARSE_ADS_SCRIPT);