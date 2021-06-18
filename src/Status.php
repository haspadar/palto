<?php
namespace Palto;

class Status
{

    public static function getMySqlDirectory(\MeekroDB $db): string
    {
        if (class_exists('DB')) {
            $variables = $db->query('SHOW VARIABLES WHERE Variable_Name LIKE "%dir"');
            foreach ($variables as $variable) {
                if ($variable['Variable_name'] == 'datadir') {
                    return $variable['Value'];
                }
            }
        }

        return '';
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
}