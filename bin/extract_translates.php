#!/usr/bin/php
<?php

use Palto\Counters;
use Palto\Translates;

require_once __DIR__ . '/autoload_require_composer.php';

$extractedTranslates = Translates::extractTranslates();
$isRussian = mb_substr($extractedTranslates['html_lang'], 0, 2) == 'ru';
$fileName = \Palto\Directory::getConfigsDirectory() . ($isRussian ? '/translates.russian.php' : '/translates.english.php');
$defaultTranslates = require_once $fileName;
Translates::saveTranslates($extractedTranslates, $defaultTranslates, $fileName);

$extractedCounters = Counters::extractCounters();
Counters::saveCounters($extractedCounters, \Palto\Directory::getConfigsDirectory() . '/counters.php');
