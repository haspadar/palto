<?php

namespace Palto;

class Directory
{
    public const PARSE_CATEGORIES_SCRIPT = 'parse_categories.php';

    public const PARSE_ADS_SCRIPT = 'parse_ads.php';

    public const STATIC_LAYOUTS_SCRIPT = 'static_layouts.php';

    public const LAYOUT_LIST = 'list.php';

    public const LAYOUT_AD = 'ad.php';

    public const LAYOUT_404 = '404.php';

    private static string $rootDirectory;

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
        return self::getRootDirectory() . '/configs';
    }

    public static function getLayoutsDirectory(): string
    {
        return self::getRootDirectory() . '/layouts';
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
}