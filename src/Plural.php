<?php

namespace Palto;

class Plural
{
    public static function get(int $count, string $formFor1, string $formFor2, string $formFor5): string
    {
        $lastNumber = self::getLastNumber($count);
        if ($lastNumber % 10 == 1 && $count % 100 != 11) {
            $form = $formFor1;
        } elseif (in_array($lastNumber % 10, [2, 3, 4]) && !in_array($count % 100, [12, 13, 14])) {
            $form = $formFor2;
        } else {
            $form = $formFor5;
        }

        return $form;
    }

    private static function getLastNumber($count): int
    {
        return mb_substr($count, -1);
    }
}