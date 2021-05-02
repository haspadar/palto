<?php

use Palto\Palto;
use Pylesos\PylesosService;

$rootDirectory = require_once 'autoload.php';
$palto = new Palto($rootDirectory);
if (isset($_GET['query']) && $_GET['query']) {
    $query = $_GET['query'];
    $query = str_replace('Cruisecontrol', 'Cruise control', $query);
    $videoId = $palto->parseYoutubeVideoId($query);
    echo json_encode(['video_id' => $videoId]);
}
