<?php

namespace Palto;

class Parser
{
    public static function filterPrice(string $price): float
    {
        $filtered = floatval(strtr($price, [',' => '', ' ' => '']));

        return min($filtered, 99999999.99);
    }

    public static function getDonorUrl(): string
    {
        if (isset($_SERVER['argv'][1])) {
            $parsed = parse_url($_SERVER['argv'][1]);

            return $parsed['scheme'] . '://' . $parsed['host'];
        }

        return '';
    }

    public static function checkDonorUrl()
    {
        if (!isset($_SERVER['argv'])) {
            exit('Укажите первым параметром URL страницы, например: php parse_ads.php https://losangeles.craigslist.org' . PHP_EOL);
        }
    }
}
