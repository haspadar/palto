<?php

use Palto\Palto;
use Pylesos\PylesosService;

if (file_exists('../vendor/autoload.php')) {
    $rootDirectory = '..';
} else {
    $rootDirectory = '../..';
}

require_once $rootDirectory . '/vendor/autoload.php';
$palto = new Palto($rootDirectory);
if (isset($_GET['query']) && $_GET['query']) {
    $query = $_GET['query'];
    $query = str_replace('Cruisecontrol', 'Cruise control', $query);
    $videoId = $palto->parseYoutubeVideoId($query);
    echo json_encode(['video_id' => $videoId]);
}
