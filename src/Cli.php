<?php

namespace Palto;

class Cli
{
    const LINUX_USER = 'km';

    const MAC_USER = 'haspadar';

    public static function isCli(): bool
    {
        return php_sapi_name() === 'cli';
    }

    public static function isSudo(): bool
    {
        return posix_getuid() == 0;
    }

    public static function isCron(): bool
    {
        return self::isCli() && !isset($_SERVER['TERM']);
    }

    public static function reloadNginx(): string
    {
        return 'service nginx reload';
    }

    public static function restartNginx(): string
    {
        if (self::isLinux()) {
            return 'service nginx restart';
        }

        return self::ignoreMac();
    }

    public static function createDatabase(string $databaseName, string $databaseUsername, string $password): string
    {
        $command = implode(
            '', [
                "DROP DATABASE IF EXISTS $databaseName;",
                "CREATE DATABASE $databaseName CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;",
                "DROP USER IF EXISTS '$databaseUsername'@'localhost';",
                "CREATE USER '$databaseUsername'@'localhost' IDENTIFIED BY '$password';",
                "GRANT ALL PRIVILEGES ON $databaseName.* TO '$databaseUsername'@'localhost';"
            ]
        );
        $dbDirectory = Directory::getDbDirectory();

        return 'mysql -e "' . $command . '"'
            . " && mysql $databaseName < $dbDirectory/palto.sql";
    }

    public static function generatePassword(int $length = 12): string
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

        return substr(str_shuffle($chars), 0, $length);
    }

    public static function safeAddCron(): string
    {
        if (self::isLinux()) {
            $cronFilePath = '/etc/crontab';
            $commandComment = '#Every minute';
            $command = "* * * * * root cd ' . Directory::getRootDirectory() . ' && vendor/bin/crunz schedule:run'";
            $cronFileContent = file_get_contents($cronFilePath);
            $isCommandExists = mb_strpos($cronFileContent, $command) !== false;
            if (!$isCommandExists) {
                return "echo '$commandComment\n$command\n' >> $cronFilePath";
            } else {
                return '';
            }
        }

        return self::ignoreMac();
    }

    public static function isEnvAuthEnabled(): bool
    {
        return file_exists(Directory::getRootDirectory() . '/.env')
            && mb_strpos(file_get_contents(Directory::getRootDirectory() . '/.env'), 'AUTH=1') !== false;
    }

    public static function generateEnv(string $databaseName, string $databaseUser, string $databasePassword): string
    {
        $replaces = [
            'DB_USER=' => 'DB_USER=' . $databaseUser,
            'DB_PASSWORD=' => 'DB_PASSWORD=' . $databasePassword,
            'DB_NAME=' => 'DB_NAME=' . $databaseName,
            'DOMAIN_URL=' => 'DOMAIN_URL=https://www.' . Directory::getProjectName()
        ];
        $content = strtr(
            file_get_contents(Directory::getConfigsDirectory() . '/.env'),
            $replaces
        );
        $tmp = '/tmp/install_' . md5(time());
        file_put_contents($tmp, $content);

        return self::asUser("cp $tmp " . Directory::getRootDirectory() . '/.env');
    }

    public static function updatePhinx(string $databaseName, string $databaseUser, string $databasePassword): string
    {
        $content = strtr(file_get_contents(Directory::getConfigsDirectory(). '/phinx.php'), [
            'production_db' => $databaseName,
            'production_user' => $databaseUser,
            'production_pass' => $databasePassword,
        ]);

        return "echo \"$content\" > " . Directory::getRootDirectory() . '/phinx.php';
    }

    public static function updateNginx(): string
    {
        if (self::isLinux()) {
            $nginxConfig = file_get_contents(Directory::getConfigsDirectory() . '/nginx/nginx.conf');

            return "echo '$nginxConfig' > /etc/nginx/nginx.conf";
        }

        return self::ignoreMac();
    }

    public static function safeLinkNginxDomain(): string
    {
        if (self::isLinux()) {
            $projectName = Directory::getProjectName();

            return self::asUser("ln -s /etc/nginx/sites-available/$projectName /etc/nginx/sites-enabled/$projectName");
        }

        return self::ignoreMac();
    }

    public static function updateNginxDomain(string $databaseName): string
    {
        if (self::isLinux()) {
            $projectName = Directory::getProjectName();
            $nginxDomain = sprintf(
                file_get_contents(Directory::getConfigsDirectory() . '/nginx/domain'),
                Directory::getRootDirectory(),
                "$projectName *.$projectName",
                intval(self::getPhpVersion()),
                $databaseName,
                $databaseName,
                $projectName
            );

            $filename = Config::getNginxDomainFilename();

            return "echo '$nginxDomain' > $filename";
        }

        return self::ignoreMac();
    }

    public static function updateNginxPhpFpm(): string
    {
        if (self::isLinux()) {
            $phpVersion = self::getPhpVersion();
            $phpMajorVersion = intval($phpVersion);
            $nginxPhpFpmConfig = sprintf(
                file_get_contents(Directory::getConfigsDirectory() . '/nginx/php-fpm.conf'),
                $phpMajorVersion,
                $phpVersion
            );

            return "echo '$nginxPhpFpmConfig' > /etc/nginx/conf.d/php$phpMajorVersion-fpm.conf";
        }

        return self::ignoreMac();
    }

    public static function updateHtpasswd(): string
    {
        if (self::isLinux()) {
            $configsPath = Directory::getConfigsDirectory();
            $rootPath = Directory::getRootDirectory();

            return "cp -R $configsPath/.htpasswd $rootPath/";
        }

        return self::ignoreMac();
    }

    public static function copyComposerJson(): string
    {
        $rootDirectory = Directory::getRootDirectory();
        $configsDirectory = Directory::getConfigsDirectory();

        return "cp $configsDirectory/composer.json $rootDirectory";
    }

    public static function safeCopyLayouts(): string
    {
        $rootDirectory = Directory::getRootDirectory();

        return "cp -R -n $rootDirectory/structure/layouts $rootDirectory";
    }

    public static function safeCopyCss(): string
    {
        $rootDirectory = Directory::getRootDirectory();

        return "cp -R -n $rootDirectory/structure/public/css $rootDirectory/public/";
    }

    public static function safeCopyImg(): string
    {
        $rootDirectory = Directory::getRootDirectory();

        return "cp -R -n $rootDirectory/structure/public/img $rootDirectory/public/";
    }

    public static function ignoreMac(): string
    {
        return 'echo "Ignored for Mac"';
    }

    public static function runCommands(array $commands)
    {
        foreach ($commands as $comment => $command) {
            $hasComment = !is_numeric($comment);
            if ($hasComment) {
                Logger::info($comment);
            }

            if (!$command) {
                Logger::warning("Ignored empty command");
            } elseif ($hasComment && $command == Cli::ignoreMac()) {
                Logger::warning(`$command`);
            } elseif ($command != Cli::ignoreMac()) {
                Logger::debug(`$command`);
            }
        }
    }

    public static function updatePermissions(string $path): string
    {
        if (self::isLinux()) {
            return "chown -R \"km\" $path";
        } else {
            return "chown -R \"haspadar\" $path";
        }
    }

    public static function isLinux(): bool
    {
        return PHP_OS == 'Linux';
    }

    private static function getPhpVersion(): string
    {
        $output = `php -v`;
        $outputLines = explode(' ', $output);
        $versionParts = explode('.', $outputLines[1]);

        return $versionParts[0] . '.' . $versionParts[1];
    }

    public static function updateHost(): string
    {
        if (self::isLinux()) {
            $hostsFilePath = '/etc/hosts';
            $hostLine = '127.0.0.1 ' . Directory::getProjectName();
            $hostsContent = file_get_contents($hostsFilePath);
            $isHostExists = mb_strpos($hostsContent, $hostLine) !== false;
            if (!$isHostExists) {
                return "echo '$hostLine\n' >> $hostsFilePath";
            }

            return '';
        } else {
            return self::ignoreMac();
        }
    }

    public static function safeCopyParseScripts(): string
    {
        $rootDirectory = Directory::getRootDirectory();

        return self::asUser("cp -n $rootDirectory/structure/" . Directory::PARSE_CATEGORIES_SCRIPT . " $rootDirectory/"
            . " && cp -n $rootDirectory/structure/" . Directory::PARSE_ADS_SCRIPT . " $rootDirectory/");
    }

    public static function safeCopyTranslates(): string
    {
        $rootDirectory = Directory::getRootDirectory();

        return self::asUser("cp -n $rootDirectory/structure/" . Directory::TRANSLATES_SCRIPT . " $rootDirectory/");
    }

    public static function checkSudo()
    {
        if (!self::isSudo()) {
            Logger::error('Run with sudo');

            exit;
        }
    }

    private static function isMac(): bool
    {
        return PHP_OS == 'Darwin';
    }

    private static function asUser(string $command): string
    {
        if (self::isLinux()) {
            $user = self::LINUX_USER;

            return "su -c \"$command\" -s /bin/sh $user";
        } else {
            $user = self::MAC_USER;

            return "sudo -u $user -i $command";
        }
    }
}