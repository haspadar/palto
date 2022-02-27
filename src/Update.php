<?php

namespace Palto;

class Update
{
    public static function run()
    {
        $databaseName = Config::get('DB_NAME');
        $databaseUsername = Config::get('DB_USER');
        $databasePassword = Config::get('DB_PASSWORD');
        Cli::runCommands([
            'Copy Translates' => Cli::safeCopyTranslates(),
            'Copy Counters' => Cli::safeCopyCounters(),
            'Copy pylesos env' => Cli::safeCopyPylesosEnv(),
            'Copy layouts env' => Cli::safeCopyLayoutsEnv(),
            'Copy CSS' => Cli::safeCopyCss(),
            'Add Cron' => Cli::safeAddCron(),
            'Update Phinx' => Cli::updatePhinx($databaseName, $databaseUsername, $databasePassword),
            'Update Htpasswd' => Cli::updateHtpasswd(),
            'Update Permissions' => Cli::updatePermissions(Directory::getRootDirectory()),
            Cli::updatePermissions('/etc/nginx/sites-available', 'www-data')
        ]);
    }

    public static function replaceCode(array $replaces)
    {
        foreach ($replaces as $fileMask => $fileReplaces) {
            if (str_ends_with($fileMask, '*')) {
                $files = Directory::getDirectoryFilesRecursive(substr($fileMask, 0, -2));
                foreach ($files as $file) {
                    self::replaceFileCode($file, $fileReplaces);
                }
            } else {
                self::replaceFileCode($fileMask, $fileReplaces);
            }
        }
    }

    private static function replaceFileCode(string $file, array $fileReplaces)
    {
        $content = file_get_contents(Directory::getRootDirectory() . '/' . $file);
        foreach ($fileReplaces as $from => $to) {
            $dotsParts = explode('...', $from);
            $isReplacedByDots = count($dotsParts) == 2;
            if ($isReplacedByDots) {
                $beforeDotsPart = $dotsParts[0];
                $afterDotsPart = $dotsParts[1] ?: ';';
                $start = mb_strpos($content, $beforeDotsPart);
                $finish = mb_strpos($content, $afterDotsPart, $start);
                if ($start !== false && $finish !== false) {
                    $content = mb_substr($content, 0, $start)
                        . $to
                        . mb_substr($content, $finish);
                }
            } else {
                $content = str_replace($from, $to, $content);
            }
        }

        file_put_contents(Directory::getRootDirectory() . '/' . $file, $content);
        Logger::info('Replaced ' . $file);
    }
}