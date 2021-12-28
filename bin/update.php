#!/usr/bin/php
<?php

use Palto\Update;

require_once '../vendor/autoload.php';

$update = new Update();
$update->run();