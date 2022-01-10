<?php

use Palto\Moderation;
use Palto\Palto;

require_once '../../vendor/autoload.php';
\Palto\Auth::check();

if (is_numeric($_POST['id'])) {
    $id = intval($_POST['id']);
    Moderation::sendRemovedComplaintMail($id);
    Moderation::removeComplaintUser($id);
    echo true;
} else {
    $ids = explode(',', $_POST['id']);
    $lastResponse = true;
    foreach ($ids as $id) {
        $id = intval($id);
        Moderation::sendRemovedComplaintMail($id);
        Moderation::removeComplaintUser($id);
    }

    echo true;
}