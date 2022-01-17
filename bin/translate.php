#!/usr/bin/php
<?php

use Palto\Backup;
use Palto\Update;

require_once __DIR__ . '/autoload_require_composer.php';

$translates = require_once 'translates.php';
$codes = \Palto\Yandex::getLanguageCodes();
\Palto\Debug::dump($codes);exit;
$translate = \Palto\Yandex::translate('Привет','ru', 'en');
\Palto\Debug::dump($translate);