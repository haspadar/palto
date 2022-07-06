<?php

namespace Palto;

class Auth
{
    public static function isLogged(): bool
    {
        return !empty($_SERVER['PHP_AUTH_USER']);
    }

    public static function check()
    {
        if (!self::isLogged()) {
            self::showAuthForm();
        } else {
            $login = $_SERVER['PHP_AUTH_USER'];
            $password = $_SERVER['PHP_AUTH_PW'];
            if ($login != Settings::getByName('auth_login')
                || $password != Settings::getByName('auth_password')
            ) {
                self::showAuthForm();
            }
        }
    }

    private static function showAuthForm()
    {
        header('WWW-Authenticate: Basic realm="My Realm"');
        header('HTTP/1.0 401 Unauthorized');
        echo "Access denied!" . PHP_EOL;
        exit;
    }
}