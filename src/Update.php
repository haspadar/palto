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
            'Copy Layouts' => Cli::safeCopyLayouts(),
            'Copy CSS' => Cli::safeCopyCss(),
            'Copy Images' => Cli::safeCopyImg(),
            'Copy Crunz' => Cli::copyCrunz(),
            Cli::safeLinkCrunzTasks(),
            'Copy composer.json' => Cli::copyComposerJson(),
            'Update PhpUnit' => Cli::safeLinkPhpUnit(),
            Cli::safeLinkTests(),
            'Add Cron' => Cli::safeAddCron(),
            'Update Phinx' => Cli::updatePhinx($databaseName, $databaseUsername, $databasePassword),
            'Download Adminer' => Cli::downloadAdminer(),
            'Update Htpasswd' => Cli::updateHtpasswd(),
        ]);
    }

    public static function replaceCode(array $replaces)
    {
        foreach ($replaces as $file => $fileReplaces) {
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
}