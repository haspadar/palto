<?php

namespace Palto;

class Russian
{
    public static function month(string $monthNumber): string
    {
        $titles = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'ноября', 'декабря'];

        return $titles[intval($monthNumber) + 1];
    }
}