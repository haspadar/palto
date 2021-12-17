<?php

namespace Palto;

use Palto\Router\Ad;
use Palto\Router\Category;
use Palto\Router\Region;
use Palto\Router\Router;
use Palto\Router\Standard;

class Routers
{
    public static function create(string $url, array $standardRoutes): Router
    {
        $path = self::getPath($url);
        $pageNumber = self::getPageNumber($url);
        if (in_array($path, array_keys($standardRoutes))) {
            $router = new Standard(
                $path,
                $standardRoutes[$path],
                $pageNumber,
                self::getQueryParameters($url)
            );
        } elseif (self::getParts($path) == 1) {
            $router = new Region(
                $path,
                self::getRegionUrl($path),
                $pageNumber,
                self::getQueryParameters($url)
            );
        } elseif (self::isAdPage($path)) {
            $router = new Ad(
                $path,
                self::getRegionUrl($path),
                self::getCategoriesUrls($path),
                self::getAdId($path),
                self::getQueryParameters($url)
            );
        } else {
            $router = new Category(
                $path,
                self::getRegionUrl($path),
                self::getCategoriesUrls($path),
                $pageNumber,
                self::getQueryParameters($url)
            );
        }

        return $router;
    }

    private static function getParts(string $path): array
    {
        return array_values(array_filter(explode('/', $path)));
    }

    private static function getPath(string $url): string
    {
        $path = parse_url($url)['path'];
        if (mb_substr($path, -1) == '/' && $path != '/') {
            $path = mb_substr($path, 0, -1);
        }

        if (self::hasUrlPageNumber($path)) {
            $path = self::withoutPageNumber($path);
        }

        return $path;
    }

    private static function getAdId(string $path): int
    {
        $last = self::getLastPart($path);

        return mb_substr($last, 2);
    }

    private static function isAdPage(string $path): bool
    {
        $last = self::getLastPart($path);

        return mb_substr($last, 0, 2) == 'ad' && is_numeric(self::getAdId($path));
    }

    private static function getPageNumber(string $path): int
    {
        $parts = self::getUrlParts($path);
        if (self::hasUrlPageNumber($path)) {
            $pageNumber = $parts[count($parts) - 1];
        } else {
            $pageNumber = 1;
        }

        return $pageNumber;
    }

    private static function hasUrlPageNumber(string $url): bool
    {
        $parts = self::getUrlParts($url);

        return isset($parts[count($parts) - 1]) && is_numeric($parts[count($parts) - 1]);
    }

    private static function withoutPageNumber(string $path): string
    {
        if (self::hasUrlPageNumber($path)) {
            $parts = self::getUrlParts($path);
            array_pop($parts);

            return implode('/', array_filter($parts));
        }

        return $path;
    }

    private static function getQueryParameters(string $url): array
    {
        $path = parse_url($url);
        parse_str($path['query'] ?? '', $parameters);
        foreach ($parameters as $name => &$value) {
            $value = self::filter($value);
        }

        return $parameters;
    }

    private static function getUrlParts(string $url): array
    {
        return array_values(array_filter(explode('/', $url)));
    }

    private static function getCategoriesUrls(string $path): array
    {
        $parts = self::getUrlParts($path);
        array_shift($parts);
        if (self::isAdPage($path)) {
            array_pop($parts);
        }

        return array_values(array_filter($parts));
    }

    private static function getRegionUrl(string $path): string
    {
        $parts = self::getUrlParts($path);

        return $parts[0];
    }

    private static function getLastPart(string $path): string
    {
        $parts = self::getUrlParts($path);

        return $parts[count($parts) - 1];
    }

    private static function filter(string $value): string
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