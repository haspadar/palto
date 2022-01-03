#!/usr/bin/php
<?php

use Palto\Backup;
use Palto\Update;

require_once __DIR__ . '/../vendor/autoload.php';

Backup::createArchive();
Update::run();