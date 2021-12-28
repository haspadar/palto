#!/usr/bin/php
<?php

use Palto\Backup;
use Palto\Logger;

require_once '../vendor/autoload.php';

$backupName = Backup::createArchive();