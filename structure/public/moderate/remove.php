<?php

use Palto\Moderation;
use Palto\Palto;

$rootDirectory = require_once '../autoload.php';
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