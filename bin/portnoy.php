#!/usr/bin/php
<?php

use Palto\Cli;
use Palto\Install;
use Palto\Portnoy;

require_once __DIR__ . '/autoload_require_composer.php';

Cli::checkSudo();
Portnoy::run();