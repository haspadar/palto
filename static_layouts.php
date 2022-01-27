<?php

return [
    '/' => \Palto\Config::get('HOT_LAYOUT') ? 'hot.php' : 'index.php',
    '/registration' => 'registration.php',
    '/regions' => 'regions-list.php',
    '/categories' => 'categories-list.php',
    '/search' => 'search-list.php',
];