<?php

namespace Palto;

class Backup
{
    public static function createArchive(): string
    {
        $files = self::getFiles();
        $time = (new \DateTime())->format('Y-m-d');
        if (!file_exists(Directory::getRootDirectory() . '/backups/')) {
            mkdir(Directory::getRootDirectory() . '/backups/');
        }

        $archiveName = Directory::getRootDirectory() . '/backups/' . Directory::getProjectName() . '-' . $time . '.zip';
        $isBackupCreated = Backup::createFilesArchive($archiveName, $files);
        if ($isBackupCreated) {
            Logger::info('Backup ' . $archiveName . ' created');

            return $archiveName;
        } else {
            Logger::error('Can\'t create archive');

            return '';
        }
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
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'file' => new \CurlFile($backupName, 'application/zip')
        ]);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    private static function getFiles(): array
    {
        $time = (new \DateTime())->format('Y-m-dTH:i:s');
        $archiveDirectory = Directory::getProjectName() . '-' . $time;
        $layoutFiles = self::getDirectoryFiles(Directory::getRootDirectory() . '/layouts/*', $archiveDirectory . '/');
        $cssFiles = self::getDirectoryFiles(Directory::getRootDirectory() . '/public/css/*', $archiveDirectory . '/');
        $imgFiles = self::getDirectoryFiles(Directory::getRootDirectory() . '/public/img/*', $archiveDirectory . '/');

        return array_merge([
            Directory::getRootDirectory() . '/' . Directory::PARSE_CATEGORIES_SCRIPT => $archiveDirectory . '/' . Directory::PARSE_CATEGORIES_SCRIPT,
            Directory::getRootDirectory() . '/' . Directory::PARSE_ADS_SCRIPT => $archiveDirectory . '/' . Directory::PARSE_ADS_SCRIPT,
        ],
            $layoutFiles,
            $cssFiles,
            $imgFiles
        );
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
                        $files[$directoryFile] = $directoryFile;
                    }
                }
            }
        }

        foreach ($files as &$file) {
            $file = $prefixPath . strtr($file, [
                Directory::getRootDirectory() . '/' => ''
            ]);
        }

        return $files;
    }
}