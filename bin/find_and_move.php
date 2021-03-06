#!/usr/bin/php
<?php

use Palto\Categories;
use Palto\Category;
use Palto\Logger;

require_once __DIR__ . '/autoload_require_composer.php';

\Palto\Logger::info('Find and move started');
if (Categories::getUndefinedAll()) {
    \Palto\Synonyms::findAndMoveAds(Categories::getUndefinedAll());
} else {
    \Palto\Logger::warning('Undefined categories not found');
}
