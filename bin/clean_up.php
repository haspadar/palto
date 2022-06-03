#!/usr/bin/php
<?php

use Palto\Backup;
use Palto\Debug;
use Palto\Model\Categories;
use Palto\Update;

require_once __DIR__ . '/autoload_require_composer.php';

\Palto\Logger::info('Ads clean up started');
\Palto\Ads::cleanUp('-11 MONTH');
