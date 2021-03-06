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

    public static function getParsersDirectory(): string
    {
        return self::getRootDirectory() . '/parsers';
    }

    public static function getParseCategoriesScript(): string
    {
        return self::getParsersDirectory() . '/categories/' . Settings::getByName('categories_parser');
    }

    public static function getParseAdsScript(): string
    {
        return self::getParsersDirectory() . '/ads/' . Settings::getByName('ads_parser');
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
            Directory::getFilesWithDirectories(self::getLayoutsDirectory()),
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



//    public static function getLogTypes(string $name): array
//    {
//        $files = self::getDirectories(self::getLogsDirectory() . '/' . $name);
//        $types = [];
//        foreach ($files as $file) {
//            $parts = explode('-', $file);
//            if (!in_array($parts[0], $types)) {
//                $types[] = $parts[0];
//            }
//        }
//
//        return $types;
//    }

    public static function getLogsDirectory(): string
    {
        return self::getRootDirectory() . '/logs';
    }

    public static function getLogsDirectories(): array
    {
        $directories = self::getFilesWithDirectories(self::getLogsDirectory());

        return array_filter($directories, function (string $directory) {
            return is_dir(self::getLogsDirectory() . '/' . $directory);
        });
    }

    public static function getPaltoDirectories(string $directory = '/var/www'): array
    {
        return array_values(array_filter(
            scandir($directory),
            fn($iterateDirectory) => file_exists($directory . '/' . $iterateDirectory . '/configs/.env')
        ));
    }

    public static function getKarmanTemplatesDirectory(): string
    {
        return self::getTemplatesDirectory() . '/karman';
    }

    public static function getTemplatesDirectory(): string
    {
        return self::getRootDirectory() . '/templates';
    }

    public static function getThemeTemplatesDirectory(): string
    {
        return self::getTemplatesDirectory() . '/' . Settings::getByName('template_theme');
    }

    public static function getThemePublicDirectory(): string
    {
        return '/themes/' . Settings::getByName('template_theme');
    }

    public static function getFilesWithoutDirectories(string $directory): array
    {
        return array_values(array_filter(
            self::getFilesWithDirectories($directory),
            fn ($file) => !is_dir($directory . '/' . $file)
        ));
    }

    public static function getFilesWithDirectories(string $directory): array
    {
        return array_values(array_filter(
            scandir($directory),
            fn($iterateDirectory) => !in_array($iterateDirectory, ['.', '..'])
        ));
    }

    public static function getDirectoryFiles(string $directory): array
    {
        $files = scandir($directory);


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