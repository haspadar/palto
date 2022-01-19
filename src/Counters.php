<?php

namespace Palto;

class Counters
{
    /**
     * @var null[]|string[]
     */
    private static array $counters;

    public static function get(string $name): string
    {
        $counters = self::getCounters();
        $counter = $counters[$name] ?? '';

        return $counter ?: $name;
    }

    private static function getCounters(): array
    {
        if (!isset(self::$counters)) {
            self::$counters = require_once Directory::getConfigsDirectory() . '/counters.php';
        }

        return self::$counters;
    }
}