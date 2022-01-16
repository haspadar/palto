<?php

use Palto\Complaints;

require_once '../../vendor/autoload.php';
\Palto\Auth::check();
if (is_numeric($_POST['id'])) {
    $id = intval($_POST['id']);
    Complaints::ignoreComplaint($id);
    echo true;
} else {
    $ids = explode(',', $_POST['id']);
    $lastResponse = true;
    foreach ($ids as $id) {
        $id = intval($id);
        Complaints::ignoreComplaint($id);
    }

    echo true;
}