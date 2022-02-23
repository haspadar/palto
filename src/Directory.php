<?php

namespace Palto;

class Directory
{
    public const COUNTERS_SCRIPT = 'counters.php';

    public const TRANSLATES_SCRIPT = 'translates.php';

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

    public static function getProjectShortName(): string
    {
        $name = self::getProjectName();
        $parts = explode('.', $name);

        return $parts[0];
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

    public static function getStructureLayoutsDirectory(): string
    {
        return self::getStructureDirectory() . '/layouts';
    }

    public static function getStructureConfigsDirectory(): string
    {
        return self::getStructureDirectory() . '/configs';
    }

    public static function getStructureDirectory(): string
    {
        return self::getRootDirectory() . '/structure';
    }

    public static function getThemes(): array
    {
        return array_filter(
            Directory::getDirectories(self::getLayoutsDirectory()),
            fn($directory) => substr($directory, -6) == '_theme'
        );
    }

    public static function getLayoutsDirectory(): string
    {
        $layoutsDirectory = isset($_GET['layouts']) ? $_GET['layouts'] : 'structure_layouts';

        return self::getRootDirectory() . '/' . $layoutsDirectory;
    }

    public static function getTestsDirectory(): string
    {
        return self::getRootDirectory() . '/tests';
    }

    public static function getParseCategoriesFile(): string
    {
        return self::getRootDirectory() . '/' . self::PARSE_CATEGORIES_SCRIPT;
    }

    public static function getParseAdsFile(): string
    {
        return self::getRootDirectory() . '/' . self::PARSE_ADS_SCRIPT;
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

    public static function getPaltoDirectories(string $directory = '/var/www'): array
    {
        return array_values(array_filter(
            scandir($directory),
            fn($iterateDirectory) => file_exists($directory . '/' . $iterateDirectory . '/configs/.env')
        ));
    }

    public static function getDirectories(string $directory): array
    {
        return array_values(array_filter(
            scandir($directory),
            fn($iterateDirectory) => !in_array($iterateDirectory, ['.', '..'])
        ));
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