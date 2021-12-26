<?php

use Palto\Moderation;

require_once '../autoload.php';
\Palto\Auth::check();
if (is_numeric($_POST['id'])) {
    $id = intval($_POST['id']);
    Moderation::ignoreComplaint($id);
    echo true;
} else {
    $ids = explode(',', $_POST['id']);
    $lastResponse = true;
    foreach ($ids as $id) {
        $id = intval($id);
        Moderation::ignoreComplaint($id);
    }

    echo true;
}