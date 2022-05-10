#!/usr/bin/php
<?php

use Palto\Categories;

require_once __DIR__ . '/autoload_require_composer.php';

$synonym =\Palto\Synonyms::getByTitle('golden retriever');
\Palto\Debug::dump($synonym, '$synonym');
$ad =\Palto\Ads::getById(115526);
\Palto\Debug::dump($ad, '$ad');
\Palto\Debug::dump(\Palto\Synonyms::hasAdSynonyms($ad, [$synonym], 'title'));
exit;
if (Categories::getUndefinedAll()) {
    \Palto\Synonyms::findAndMoveAds(Categories::getLiveCategories());
} else {
    \Palto\Logger::warning('Undefined categories not found');
}
