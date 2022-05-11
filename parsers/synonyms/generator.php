<?php

use ICanBoogie\Inflector;
use Palto\Model\Categories;

require realpath(dirname(__DIR__) . '/../') . '/vendor/autoload.php';

$categories = Categories::getDb()->query("SELECT * FROM categories WHERE url NOT LIKE %s", 'undefined%');
foreach ($categories as $key => $category) {
    echo 'Category ' . ($key + 1) . '/' . count($categories);
    $title = $category['title'];
    $synonyms = array_column(
        \Palto\Model\Synonyms::getDb()->query('SELECT * FROM synonyms WHERE category_id = %d', $category['id']),
        'title'
    );
    $combinations = getCombinations(array_values(array_filter(array_unique(array_merge($synonyms, [$title])))));
    foreach ($combinations as $combination) {
        \Palto\Model\Synonyms::add($combination, $category['id']);
    }
}

function getCombinations(array $synonyms): array
{
    $combinations = array_map(fn($form) => mb_strtolower($form), $synonyms);
    $inflector = Inflector::get('en');
    foreach ($synonyms as $uniqueForm) {
        $plural = $inflector->pluralize($uniqueForm);
        if ($plural != $uniqueForm) {
            $combinations[] = $plural;
        }

        $singular = $inflector->singularize($uniqueForm);
        if ($singular != $uniqueForm) {
            $combinations[] = $singular;
        }
    }

    if (count($combinations) != count($synonyms)) {
        \Palto\Logger::debug('Found ' . (count($combinations) - count($synonyms)) . ' new combinations');
    }

    return $combinations;
}
