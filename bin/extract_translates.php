#!/usr/bin/php
<?php

use Palto\Counters;
use Palto\Translates;

require_once __DIR__ . '/autoload_require_composer.php';

$extractedTranslates = Translates::extractTranslates();
$isRussian = mb_substr($extractedTranslates['html_lang'], 0, 2) == 'ru';
$fileName = \Palto\Directory::getConfigsDirectory() . ($isRussian ? '/translates.russian.php' : '/translates.english.php');
$defaultTranslates = require_once $fileName;
$extractedTranslates['ad_h1'] = ':AD <span style="color:#999"> ' . $defaultTranslates['в'] . ' :ADDRESS_WITH_REGION ' . ($isRussian ? 'c ' : 'from ') . \Palto\Directory::getProjectShortName();
Translates::saveTranslates($extractedTranslates, $defaultTranslates, $fileName);

$extractedCounters = Counters::extractCounters();
Counters::saveCounters($extractedCounters, \Palto\Directory::getConfigsDirectory() . '/counters.php');
