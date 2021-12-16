#!/usr/bin/php
<?php

use Palto\Install;

require_once 'vendor/autoload.php';

$install = new Install();
$install->updateProjectConfigs();