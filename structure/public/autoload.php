<?php
$path = __DIR__;
while (!file_exists($path . '/vendor') && $path != '/') {
    $path = dirname($path);
}

require_once $path . '/vendor/autoload.php';

return $path;