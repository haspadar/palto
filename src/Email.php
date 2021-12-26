<?php

namespace Palto;

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;

class Email
{
    public static function send(string $toEmail, string $subject, string $body)
    {
        $login = explode('@', Config::get('SMTP_EMAIL'))[0];
        $dsn = "smtp://$login:" . Config::get('SMTP_PASSWORD') . '@' . Config::get('SMTP_HOST') . ':' . Config::get('SMTP_PORT');
        $transport = Transport::fromDsn($dsn);
        $mailer = new Mailer($transport);
        $email = (new \Symfony\Component\Mime\Email())
            ->from(Config::get('SMTP_EMAIL'))
            ->to($toEmail)
            ->subject($subject)
            ->html($body);
        $mailer->send($email);
    }
}