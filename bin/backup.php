#!/usr/bin/php
<?php

use Palto\Backup;

require_once __DIR__ . '/autoload_require_composer.php';

$backupName = Backup::createArchive();