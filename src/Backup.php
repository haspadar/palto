<?php
namespace Palto;

class Backup
{
    public static function createArchive(): string
    {
        $files = self::getFiles();
        $time = (new \DateTime())->format('Y-m-d');
        $archiveName = Directory::getRootDirectory() . '/backups/' . Directory::getProjectName() . '-' . $time . '.zip';
        $isBackupCreated = Backup::createFilesArchive($archiveName, $files);
        if ($isBackupCreated) {
            return $archiveName;
        }

        return '';
    }

    private static function createFilesArchive(string $archiveName, array $files): bool
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

    private static function getFiles(): array
    {
        $time = (new \DateTime())->format('Y-m-d');
        $archiveDirectory = Directory::getProjectName() . '-' . $time;
        $layoutFiles = self::getDirectoryFiles(Directory::getRootDirectory() . '/layouts/*', $archiveDirectory . '/');

        return array_merge([
            Directory::getRootDirectory() . '/' . Palto::PARSE_CATEGORIES_SCRIPT => $archiveDirectory . '/' . Palto::PARSE_CATEGORIES_SCRIPT,
            Directory::getRootDirectory() . '/' . Palto::PARSE_ADS_SCRIPT => $archiveDirectory . '/' . Palto::PARSE_ADS_SCRIPT,
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