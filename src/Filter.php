<?php

namespace Palto;

class Filter
{
    public static function shortText(string $text, int $length = 135): string
    {
        $cleanText = strip_tags($text);
        $short = mb_substr($cleanText, 0, $length);
        if ($short != $cleanText) {
            $short .= '...';
        }

        return $short;
    }

    public static function getIntArray(array $values): array
    {
        foreach ($values as &$value) {
            $value = intval($value);
        }

        return array_filter($values);
    }

    public static function getArray(array $values): array
    {
        foreach ($values as &$value) {
            $value = self::get($value);
        }

        return $values;
    }

    public static function get(string $value)
    {
        return trim(strip_tags(htmlentities(self::removeEmoji($value))));
    }

    private static function removeEmoji(string $string): string
    {
        $symbols = "\x{1F100}-\x{1F1FF}" // Enclosed Alphanumeric Supplement
            ."\x{1F300}-\x{1F5FF}" // Miscellaneous Symbols and Pictographs
            ."\x{1F600}-\x{1F64F}" //Emoticons
            ."\x{1F680}-\x{1F6FF}" // Transport And Map Symbols
            ."\x{1F900}-\x{1F9FF}" // Supplemental Symbols and Pictographs
            ."\x{2600}-\x{26FF}" // Miscellaneous Symbols
            ."\x{2700}-\x{27BF}"; // Dingbats

        return preg_replace('/['. $symbols . ']+/u', '', $string);
    }
}