<?php

namespace Palto;

class Backup
{
    public static function createArchive(): string
    {
        $archiveName = Directory::getProjectName() . '-' . (new \DateTime())->format('Y-m-d\TH:i:s');
        $files = self::getFiles($archiveName);
        if (!file_exists(Directory::getRootDirectory() . '/backups/')) {
            mkdir(Directory::getRootDirectory() . '/backups/');
        }

        $archiveName = Directory::getRootDirectory() . '/backups/' . $archiveName . '.zip';
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

    public static function sendSundukArchive()
    {
        if (Config::get('SUNDUK_URL')) {
            $backupName = Backup::createArchive();
            $response = self::sendFile($backupName, Config::get('SUNDUK_URL'));
            if ($response == 'File is uploaded successfully.') {
                Logger::info($response);
            } else {
                Logger::error($response);
            }

        } else {
            Logger::error('SUNDUK_URL is empty');
        }
    }

    private static function getFiles(string $archiveName): array
    {
        $layoutFiles = self::getDirectoryFiles(Directory::getRootDirectory() . '/layouts/*', $archiveName . '/');
        $cssFiles = self::getDirectoryFiles(Directory::getRootDirectory() . '/public/css/*', $archiveName . '/');
        $imgFiles = self::getDirectoryFiles(Directory::getRootDirectory() . '/public/img/*', $archiveName . '/');

        return array_merge([
            Directory::getRootDirectory() . '/' . Directory::PARSE_CATEGORIES_SCRIPT => $archiveName . '/' . Directory::PARSE_CATEGORIES_SCRIPT,
            Directory::getRootDirectory() . '/' . Directory::PARSE_ADS_SCRIPT => $archiveName . '/' . Directory::PARSE_ADS_SCRIPT,
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

    private static function sendFile(string $backupName, string $url): string|bool
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'file' => new \CurlFile($backupName, 'application/zip')
        ]);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}