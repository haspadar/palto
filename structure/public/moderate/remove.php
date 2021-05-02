<?php

use Palto\Moderation;
use Palto\Palto;

if (file_exists('../../vendor/autoload.php')) {
    $rootDirectory = '../..';
} else {
    $rootDirectory = '../../..';
}

require_once $rootDirectory . '/vendor/autoload.php';
$palto = new Palto($rootDirectory);
$palto->checkAuth();

if (is_numeric($_POST['id'])) {
    $id = intval($_POST['id']);
    Moderation::sendRemovedComplaintMail($palto, $id);
    Moderation::removeComplaintUser($palto->getDb(), $id);
    echo true;
} else {
    $ids = explode(',', $_POST['id']);
    $lastResponse = true;
    foreach ($ids as $id) {
        $id = intval($id);
        Moderation::sendRemovedComplaintMail($palto, $id);
        Moderation::removeComplaintUser($palto->getDb(), $id);
    }

    echo true;
}