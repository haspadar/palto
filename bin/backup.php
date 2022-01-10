#!/usr/bin/php
<?php

use Palto\Backup;
use Palto\Install;

require_once __DIR__ . '/../vendor/autoload.php';

$backupName = Backup::createArchive();