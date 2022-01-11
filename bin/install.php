#!/usr/bin/php
<?php

use Palto\Cli;
use Palto\Install;

require_once __DIR__ . '/../safe_require_composer.php';

Cli::checkSudo();
Install::run();