<?php

use Palto\Auth;
use Palto\Config;
use Palto\IP;
use Palto\Moderation;

require_once '../vendor/autoload.php';
if (Config::get('AUTH') && !IP::isLocal()) {
    Auth::check();
}

$email = Palto\Filter::get($_POST['email'] ?? '');
$message = Palto\Filter::get($_POST['message'] ?? '');
$adId = intval($_POST['ad_id'] ?? '');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response = 'Is not email';
} elseif (!$message) {
    $response = 'Message is empty';
} elseif (!$adId) {
    $response = 'Ad id is empty';
} elseif (!Moderation::getSmtpEmail()) {
    $response = 'Smtp email is empty';
} else {
    Moderation::addComplaint(
        [
            'email' => $email,
            'message' => $message,
            'ad_id' => $adId,
            'domain' => $_SERVER['HTTP_ORIGIN'],
            'page' => str_replace($_SERVER['HTTP_ORIGIN'], '', $_SERVER['HTTP_REFERER']),
            'create_time' => (new \DateTime())->format('Y-m-d H:i:s')
        ]
    );
}

echo $response ?? '1';
