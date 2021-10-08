<?php
namespace Palto;

class Backup
{
    public static function createArchive(string $archiveName, array $files): bool
    {
        $zip = new \ZipArchive;
        if ($zip->open($archiveName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            foreach ($files as $realPath => $archivePath) {
                $zip->addFile($realPath, $archivePath);
            }

            $zip->close();

            return true;
        }

        return false;
    }

    public static function createConfigArchive(): string
    {
        $archiveName = 'backups/.env.zip';
        $isBackupCreated = Backup::createArchive($archiveName, self::getConfigFiles());
        if ($isBackupCreated) {
            return $archiveName;
        }

        return '';
    }

    public static function createSundukArchive(string $projectName): string
    {
        $files = self::getSundukFiles($projectName);
        $time = (new \DateTime())->format('Y-m-d');
        $archiveName = 'backups/' . $projectName . '-' . $time . '.zip';
        $isBackupCreated = Backup::createArchive($archiveName, $files);
        if ($isBackupCreated) {
            return $archiveName;
        }

        return '';
    }

    public static function sendSundukArchive(string $backupName, string $url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'file' => new \CurlFile($backupName, 'application/zip')
        ]);
        $result = curl_exec ($ch);
        curl_close ($ch);

        return $result;
    }

    private static function getSundukFiles(string $projectName): array
    {
        $time = (new \DateTime())->format('Y-m-d');
        $archiveDirectory = $projectName . '-' . $time;
        $layoutFiles = self::getDirectoryFiles('layouts/*', $archiveDirectory . '/');

        return array_merge([
            Palto::PARSE_CATEGORIES_SCRIPT => $archiveDirectory . '/' . Palto::PARSE_CATEGORIES_SCRIPT,
            Palto::PARSE_ADS_SCRIPT => $archiveDirectory . '/' . Palto::PARSE_ADS_SCRIPT,
            'logs/parser-' . (new \DateTime())->modify('-1 DAY')->format('Y-m-d') =>
                $archiveDirectory . '/logs/parser-' . (new \DateTime())->modify('-1 DAY')->format('Y-m-d'),
        ], $layoutFiles);
    }

    private static function getConfigFiles(): array
    {
        $archiveDirectory = 'backup-env-' . (new \DateTime())->format('Y-m-d');

        return ['.env' => $archiveDirectory . '/.env'];
    }

    private static function getDirectoryFiles(string $directory, string $prefixPath = ''): array
    {
        $files = [];
        foreach (glob($directory) as $file) {
            if (file_exists($file) && is_file($file)) {
                $files[$file] = $file;
            } elseif (is_dir($file)) {
                foreach (glob($file . '/*') as $directoryFile) {
                    if (file_exists($directoryFile) && is_file($directoryFile)) {
                        $files[$directoryFile] = $prefixPath . $directoryFile;
                    }
                }
            }
        }

        return $files;
    }
}