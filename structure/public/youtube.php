<?php

use Palto\Palto;
use Pylesos\PylesosService;

require '../vendor/autoload.php';

$palto = new Palto();
if (isset($_GET['query']) && $_GET['query']) {
    $query = $_GET['query'];
    $query = str_replace('Cruisecontrol', 'Cruise control', $query);
    $videoId = $palto->parseYoutubeVideoId($query);
    echo json_encode(['video_id' => $videoId]);
}
