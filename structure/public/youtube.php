<?php

require_once 'autoload.php';

$youtube = new \Palto\Youtube();
$query = $_GET['query'] ?? '';
header('Content-Type: application/json; charset=utf-8');
if ($query) {
    echo \json_encode(['video_id' => $youtube->getVideoId($_GET['query'] ?? '')]);
} else {
    echo \json_encode(['error' => 'Query is empty']);
}