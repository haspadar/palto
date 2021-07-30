#!/usr/bin/php
<?php

use Palto\Install;

require_once 'vendor/autoload.php';

$install = new Install();
$install->run(\Palto\Palto::PARSE_SINGLE_SITE_ADS_SCRIPT);