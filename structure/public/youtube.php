<?php

use Palto\Palto;
use Pylesos\PylesosService;

require '../vendor/autoload.php';

$palto = new Palto();
if (isset($_GET['query']) && $_GET['query']) {
    $query = $_GET['query'];
    $query = str_replace('Cruisecontrol', 'Cruise control', $query);
    $html = PylesosService::download(
        'https://www.youtube.com/results?search_query=' . urlencode($query),
        $palto->getEnv()
    )->getResponse();
    $pattern = '/watch?v=';
    $videoUrlStart = strpos($html, '/watch?v=');
    if ($videoUrlStart) {
        $videoUrlFinish = strpos($html, '"', $videoUrlStart);
        $videoId = substr(
            $html,
            $videoUrlStart + strlen($pattern),
            $videoUrlFinish - $videoUrlStart - strlen($pattern)
        );
        echo json_encode(['video_id' => $videoId]);
    }
}
