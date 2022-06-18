<?php

namespace Palto;

class Logs
{
    public static function getLogLastTime(string $directory, string $type): ?\DateTime
    {
        $logs = self::getLogs($directory, $type, 1);
        Debug::dump($logs);
        if ($logs) {
            $timeFragment = str_replace('[', '', explode(']', $logs[0]['text'])[0]);
Debug::dump(new \DateTime($timeFragment));exit;
            return new \DateTime($timeFragment);
        }

        return null;
    }

    public static function getLogs(string $directory, string $type, int $limit = 30): array
    {
        $lastLogFile = self::getLastLogFile($directory, $type);

        return self::getLines($lastLogFile, $limit);
    }

    private static function getLastLogFile(string $directory, string $type): string
    {
        $files = Directory::getFilesWithDirectories(Directory::getLogsDirectory() . '/' . $directory);
        $typeFiles = array_filter(
            $files,
            fn($file) => Validator::isDateValid(self::getFileDate($file, $type))
        );
        usort($typeFiles, function($previousFile, $nextFile) use ($type) {
            $previousFileDate = self::getFileDate($previousFile, $type);
            $nextFileDate = self::getFileDate($nextFile, $type);

            return new \DateTime($previousFileDate) > new \DateTime($nextFileDate)
                ? -1
                : (new \DateTime($previousFileDate) < new \DateTime($nextFileDate)
                    ? 1
                    : 0
                );
        });

        return $typeFiles
            ? Directory::getLogsDirectory() . '/' . $directory . '/' . $typeFiles[0]
            : '';
    }

    private static function getFileDate(string $file, string $type): string
    {
        return str_replace($type . '-', '', $file);
    }

    private static function getLines(string $file, int $limit): array
    {
        if ($file) {
            $file = file($file);
            $lines = [];
            for ($i = max(0, count($file) - $limit - 1); $i < count($file); $i++) {
                $lineNumber = count($file) - $limit + $i;
                $lines[] = [
                    'line' => max($lineNumber, 1),
                    'text' => $file[$i]
                ];
            }

            return $lines;
        }

        return [];
    }
}