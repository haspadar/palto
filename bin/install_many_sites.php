#!/usr/bin/php
<?php

use Palto\Install;

require_once 'vendor/autoload.php';

$install = new Install();
$install->run(\Palto\Palto::PARSE_ADS_SCRIPT);