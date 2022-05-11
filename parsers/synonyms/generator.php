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
    $combinations = \Palto\Synonyms::generateForms(array_values(array_filter(array_unique(array_merge($synonyms, [$title])))));
    \Palto\Synonyms::add($combinations, $category['id']);
}
