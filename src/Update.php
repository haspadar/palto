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
            'Update PhpUnit' => Cli::safeLinkPhpUnit(),
            Cli::safeLinkTests(),
            'Add Cron' => Cli::safeAddCron(),
            'Update Phinx' => Cli::updatePhinx($databaseName, $databaseUsername, $databasePassword),
            'Download Adminer' => Cli::downloadAdminer(),
        ]);
    }

    public static function replaceCode(array $replaces)
    {
        foreach ($replaces as $file => $fileReplaces) {
            $content = file_get_contents(Directory::getRootDirectory() . '/' . $file);
            foreach ($fileReplaces as $from => $to) {
                $isReplaceBeforeSemicolon = mb_substr($from, -3) == '...';
                if ($isReplaceBeforeSemicolon) {
                    $start = mb_strpos($content, mb_substr($from, 0, -3));
                    $finish = mb_strpos($content, ';', $start);
                    if ($start !== false && $finish !== false) {
                        $content = mb_substr($content, 0, $start)
                            . $to
                            . mb_substr($content, $finish + 1);
                    }

                } else {
                    $content = str_replace($from, $to, $content);
                }
            }

            file_put_contents(Directory::getRootDirectory() . '/' . $file, $content);
            Logger::info('Replaced ' . $file);
        }
    }

    public static function check()
    {
        if (Config::get('CODE_WARNINGS_EMAIL')) {
            $command = Directory::getRootDirectory()
                . '/vendor/bin/phpunit '
                . Directory::getTestsDirectory();
            $response = `$command`;
            $responseRows = array_values(array_filter(explode(PHP_EOL, $response)));
            $responseLastRow = $responseRows[count($responseRows) - 1];
            $isSuccess = mb_substr($responseLastRow, 0, 2) == 'OK';
            if (!$isSuccess) {
                Email::send(Config::get('CODE_WARNINGS_EMAIL'), 'Ошибка на ' . Directory::getProjectName(), $response);
                Logger::error($response);
            } else {
                Logger::info($responseLastRow);
            }
        } else {
            Logger::error('Укажите настройку CODE_WARNINGS_EMAIL');
        }
    }

    private function getReplaceBeforeSemicolon(string $file, string $from)
    {
        $content = file_get_contents(Directory::getRootDirectory() . '/' . $file);
        $start = mb_strpos($content, $from);
        $finish = mb_strpos($content, ';', $start);
    }
}