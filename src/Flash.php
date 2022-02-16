<?php

namespace Palto;

class Flash
{
    public static function add(string $message)
    {
        setcookie('flash_message', $message, time() + 60 * 10, '/');
    }

    public static function receive(): string
    {
        $message = $_COOKIE['flash_message'] ?? '';
        unset($_COOKIE['flash_message']);
        setcookie('flash_message', null, -1, '/');

        return $message;
    }
}