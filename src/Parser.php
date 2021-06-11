<?php

namespace Palto;

class Parser
{
    public static function filterPrice(string $price): float
    {
        $filtered = floatval(strtr($price, [',' => '', ' ' => '']));

        return min($filtered, 99999999.99);
    }
}