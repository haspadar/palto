<?php

namespace Palto;

use morphos\Russian\GeographicalNamesInflection;

class Russian
{
    public static function month(string $monthNumber): string
    {
        $titles = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'ноября', 'декабря'];

        return $titles[intval($monthNumber) + 1];
    }

    public static function regionCase(string $region, string $case): string
    {
        $forms = [
            'Polska' => 'Polske'
        ];
        $region = strtr($region, $forms);

        return GeographicalNamesInflection::getCase($region, $case);
    }
}