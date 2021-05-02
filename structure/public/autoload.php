<?php
$path = __DIR__;
while (!file_exists($path . '/vendor') && $path != '/') {
    $path = dirname($path);
}

return $path;