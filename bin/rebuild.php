#!/usr/bin/php
<?php

use Palto\Backup;
use Palto\Debug;
use Palto\Model\Categories;
use Palto\Update;

require_once __DIR__ . '/autoload_require_composer.php';

\Palto\Logger::info('Categories rebuildTree started');
\Palto\Categories::rebuildTree();
\Palto\Logger::info('Categories rebuildAdsCount started');
\Palto\Categories::rebuildAdsCount();
\Palto\Logger::info('Regions rebuildTree started');
\Palto\Regions::rebuildTree();
\Palto\Logger::info('Regions rebuildAdsCount started');
\Palto\Regions::rebuildAdsCount();
\Palto\Logger::info('Live rebuild started');
\Palto\Live::rebuild();