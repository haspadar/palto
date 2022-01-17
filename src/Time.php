<?php

namespace Palto;

class Time
{
    public static function russianMonth(string $monthNumber): string
    {
        $titles = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'ноября', 'декабря'];

        return $titles[intval($monthNumber) + 1];
    }
}