#!/usr/bin/php
<?php

use Palto\Update;

require_once dirname(__DIR__) . '/public/autoload.php';

$update = new Update();
$update->run();