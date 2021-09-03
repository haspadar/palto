<?php
use Palto\Moderation;
use Palto\Palto;

$rootDirectory = require_once 'autoload.php';
$palto = new Palto($rootDirectory);
if ($palto->getEnv()['AUTH']) {
    $palto->checkAuth();
}

$email = $palto->filterString($_POST['email']);
$message = $palto->filterString($_POST['message']);
$adId = intval($_POST['ad_id']);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response = 'Is not email';
} elseif (!$message) {
    $response = 'Message is empty';
} elseif (!Moderation::getSmtpEmail($palto)) {
    $response = 'Smtp email is empty';
} else {
    Moderation::addComplaint(
        $palto, [
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
