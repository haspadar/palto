<?php

namespace Palto;

class Directory
{
    public const PARSE_CATEGORIES_SCRIPT = 'parse_categories.php';

    public const PARSE_ADS_SCRIPT = 'parse_ads.php';

    public const ROUTES_SCRIPT = 'routes.php';

    const VENDOR_HASPADAR_PALTO = 'vendor/haspadar/palto';

    private static string $rootDirectory;

    private static array $standardRoutes = [];

    public static function setRootDirectory(string $rootDirectory)
    {
        self::$rootDirectory = $rootDirectory;
    }

    public static function getProjectName(): string
    {
        $pathParts = explode('/', self::getRootDirectory());

        return $pathParts[count($pathParts) - 1];
    }

    public static function getConfigsDirectory(): string
    {
        return self::getPaltoDirectory() . '/configs';
    }

    public static function getLayoutsDirectory(): string
    {
        return self::getRootDirectory() . '/layouts';
    }

    public static function getPaltoDirectory(): string
    {
        return self::getRootDirectory() . '/' . self::VENDOR_HASPADAR_PALTO;
    }

    public static function getTestsDirectory(): string
    {
        return self::getRootDirectory() . '/tests';
    }

    public static function getRootDirectory(): string
    {
        if (!isset(self::$rootDirectory)) {
            $path = __DIR__;
            while (!file_exists($path . '/vendor') && $path != '/') {
                $path = dirname($path);
            }

            self::$rootDirectory = $path;
        }

        return self::$rootDirectory;
    }

    public static function getDirectoryFilesRecursive(string $directory): array
    {
        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));
        $files = array();
        foreach ($rii as $file) {
            if ($file->isDir()){
                continue;
            }

            $files[] = $file->getPathname();
        }

        return $files;
    }

    public static function getDbDirectory(): string
    {
        return self::getRootDirectory() . '/db';
    }

    public static function getPublicDirectory(): string
    {
        return self::getRootDirectory() . '/public';
    }

    public static function getStandardRouteLayout(string $path): string
    {
        if (in_array($path, array_keys(self::getStandardRoutes()))) {
            return self::getStandardRoutes()[$path];
        }

        return '';
    }

    private static function getStandardRoutes(): array
    {
        if (!self::$standardRoutes) {
            self::$standardRoutes = require_once self::getRootDirectory() . '/' . self::ROUTES_SCRIPT;
        }

        return self::$standardRoutes;
    }
}