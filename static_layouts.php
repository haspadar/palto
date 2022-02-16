<?php

return [
    '/' => $_GET['hot_layout'] ?? (\Palto\Config::get('HOT_LAYOUT') ? 'hot.php' : 'index.php'),
    '/registration' => 'registration.php',
    '/regions' => 'regions-list.php',
    '/categories' => 'categories-list.php'
];