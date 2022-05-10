#!/usr/bin/php
<?php

use Palto\Categories;

require_once __DIR__ . '/autoload_require_composer.php';

if (Categories::getUndefinedAll()) {
    \Palto\Synonyms::findAndMoveAds(Categories::getLiveCategories());
} else {
    \Palto\Logger::warning('Undefined categories not found');
}
