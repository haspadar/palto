<?php

namespace Palto;

class Debug
{
    public static function dump($data, string $name = '', string $ip = '')
    {
        if (!$ip || $ip == IP::get()) {
            echo '<pre>';
            if ($name) {
                var_dump($name);
            }

            var_dump($data);
        }
    }
}