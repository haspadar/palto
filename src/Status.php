<?php
namespace Palto;

use Doctrine\DBAL\Connection;

class Status
{
    const DISABLE_CACHE_ROW = 'set $no_cache 1;#disable cache';
    const ENABLE_CACHE_ROW = '#set $no_cache 1;#disable cache';

    public static function getMySqlDirectory(Connection $db): string
    {
        $variables = $db->executeQuery('SHOW VARIABLES WHERE Variable_Name LIKE "%dir"')->fetchAllKeyValue();

        return $variables['datadir'];
    }

    public static function getDirectoryUsePercent($directory): string
    {
        $result = `df -Ph $directory`;
        $lines = array_filter(explode(PHP_EOL, $result));
        $values = explode(' ', $lines[1]);
        foreach ($values as $value) {
            if (strpos($value, '%') !== false) {
                return $value;
            }
        }

        return '';
    }

    public static function getParserElapsedTime(int $pid): string
    {
        return trim(str_replace('ELAPSED', '', `ps -p $pid -o etime`));
    }

    public static function getPhpCommandPid(string $scriptName, string $directoryName): int
    {
        $pidWithCommands = `ps -eo pid,command | grep $scriptName`;
        foreach (explode(PHP_EOL, $pidWithCommands) as $pidWithCommand) {
            $pid = intval($pidWithCommand);
            $command = trim(str_replace($pid, '', $pidWithCommand));
            $hasCommandPhp = strpos($command, 'php ') !== false;
            if ($hasCommandPhp) {
                $commandPathCommandParts = explode(' ', `lsof -p $pid | grep cwd`);
                $commandPath = trim($commandPathCommandParts[count($commandPathCommandParts) - 1]);
                if (strpos($commandPath, '/' . $directoryName)) {
                    return $pid;
                }
            }
        }

        return 0;
    }

    public static function disableCache()
    {
        $name = Config::getNginxDomainFilename();
        if ($name) {
            $content = file_get_contents($name);
            $replaced = str_replace(self::ENABLE_CACHE_ROW, self::DISABLE_CACHE_ROW, $content);
            file_put_contents($name, $replaced);
        }
    }

    public static function isSiteEnabled(): bool
    {
        return \Palto\Config::get('AUTH') == 0;
    }

    public static function isCacheEnabled(): bool
    {
        $name = Config::getNginxDomainFilename();
        if ($name) {
            $nginxConfig = file_get_contents($name);

            return mb_strpos($nginxConfig, self::ENABLE_CACHE_ROW) !== false;
        }

        return false;
    }

    public static function enableCache()
    {
        $name = Config::getNginxDomainFilename();
        if ($name) {
            $content = file_get_contents($name);
            $replaced = str_replace(self::DISABLE_CACHE_ROW, self::ENABLE_CACHE_ROW, $content);
            file_put_contents($name, $replaced);
        }
    }

    public static function enableSite()
    {
        $content = file_get_contents(Directory::getRootDirectory() . '/.env');
        $replaced = str_replace('AUTH=1', 'AUTH=0', $content);
        file_put_contents(Directory::getRootDirectory() . '/.env', $replaced);
    }

    public static function disableSite()
    {
        $content = file_get_contents(Directory::getRootDirectory() . '/.env');
        $replaced = str_replace('AUTH=0', 'AUTH=1', $content);
        file_put_contents(Directory::getRootDirectory() . '/.env', $replaced);
    }
}