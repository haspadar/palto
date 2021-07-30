#!/usr/bin/php
<?php

use Palto\Install;

require_once 'vendor/autoload.php';

$install = new Install();
$install->run(\Palto\Palto::PARSE_MANY_SITES_ADS_SCRIPT);