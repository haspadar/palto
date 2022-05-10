#!/usr/bin/php
<?php

use Palto\Categories;

require_once __DIR__ . '/autoload_require_composer.php';

if (Categories::getUndefinedAll()) {
    \Palto\Synonyms::findAndMoveAds(Categories::getUndefinedAll());
} else {
    \Palto\Logger::warning('Undefined categories not found');
}
