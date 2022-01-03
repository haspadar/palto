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

    public static function safeLinkTests(): string
    {
        $paltoDirectory = Directory::getPaltoDirectory();
        $rootDirectory = Directory::getRootDirectory();

        return self::asUser("ln -s $paltoDirectory/tests $rootDirectory/");
    }

    public static function safeLinkPhpUnit(): string
    {
        $paltoDirectory = Directory::getPaltoDirectory();
        $rootDirectory = Directory::getRootDirectory();

        return self::asUser("ln -s $paltoDirectory/phpunit.xml $rootDirectory/");
    }

    public static function copyCrunz(): string
    {
        $rootDirectory = Directory::getRootDirectory();
        $configsDirectory = Directory::getConfigsDirectory();

        return self::asUser("cp $configsDirectory/crunz.yml" . " $rootDirectory/");
    }

    public static function downloadAdminer(): string
    {
        $publicDirectory = Directory::getPublicDirectory();

        return self::asUser("wget -O $publicDirectory/adminer.php https://www.adminer.org/latest-mysql-en.php");
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
        ];
        $content = strtr(
            file_get_contents(Directory::getConfigsDirectory() . '/.env'),
            $replaces
        );
        $tmp = '/tmp/install_' . md5(time());
        file_put_contents($tmp, $content);

        return self::asUser("cp $tmp " . Directory::getRootDirectory() . '/.env2');
    }

    public static function updatePhinx(string $databaseName, string $databaseUser, string $databasePassword): string
    {
        $content = strtr(file_get_contents(Directory::getConfigsDirectory(). '/phinx.php'), [
            'production_db' => $databaseName,
            'production_user' => $databaseUser,
            'production_pass' => $databasePassword,
            '%%PHINX_CONFIG_DIR%%' => Directory::VENDOR_HASPADAR_PALTO
        ]);

        return "echo \"$content\" >> " . Directory::getRootDirectory() . '/phinx.php';
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

            return "echo '$nginxDomain' > /etc/nginx/sites-available/$projectName";
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

    public static function safeCreatePublicDirectory(): string
    {
        $rootDirectory = Directory::getRootDirectory();

        return "mkdir $rootDirectory/public";
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
        $paltoDirectory = Directory::getPaltoDirectory();

        return "cp -R -n $paltoDirectory/structure/layouts $rootDirectory";
    }

    public static function safeCopyCss(): string
    {
        $rootDirectory = Directory::getRootDirectory();
        $paltoDirectory = Directory::getPaltoDirectory();

        return "cp -R -n $paltoDirectory/structure/public/css $rootDirectory/public/";
    }

    public static function safeCopyImg(): string
    {
        $rootDirectory = Directory::getRootDirectory();
        $paltoDirectory = Directory::getPaltoDirectory();

        return "cp -R -n $paltoDirectory/structure/public/img $rootDirectory/public/";
    }

    public static function safeLinkRoutes(): string
    {
        $rootDirectory = Directory::getRootDirectory();
        $paltoDirectory = Directory::getPaltoDirectory();

        return self::asUser("ln -s $paltoDirectory/structure/" . Palto::ROUTES_SCRIPT . " $rootDirectory/");
    }

    public static function safeLinkJs(): string
    {
        $rootDirectory = Directory::getRootDirectory();
        $paltoDirectory = Directory::getPaltoDirectory();

        return self::asUser("ln -s $paltoDirectory/structure/public/js $rootDirectory/public/");
    }

    public static function safeLinkModerate(): string
    {
        $rootDirectory = Directory::getRootDirectory();
        $paltoDirectory = Directory::getPaltoDirectory();

        return self::asUser("ln -s $paltoDirectory/structure/public/moderate $rootDirectory/public/");
    }

    public static function safeLinkPublicPhpScripts(): string
    {
        $rootDirectory = Directory::getRootDirectory();
        $paltoDirectory = Directory::getPaltoDirectory();

        return self::asUser("ln -s $paltoDirectory/structure/public/*.php $rootDirectory/public/");
    }

    public static function safeLinkSitemapScript(): string
    {
        $rootDirectory = Directory::getRootDirectory();
        $paltoDirectory = Directory::getPaltoDirectory();

        return self::asUser("ln -s $paltoDirectory/structure/" . Sitemap::GENERATE_SCRIPT . " $rootDirectory/");
    }

    public static function safeLinkCrunzTasks(): string
    {
        $rootDirectory = Directory::getRootDirectory();
        $paltoDirectory = Directory::getPaltoDirectory();

        return self::asUser("ln -s $paltoDirectory/tasks $rootDirectory/");
    }

    public static function safeCopyParseScripts(): string
    {
        $rootDirectory = Directory::getRootDirectory();
        $paltoDirectory = Directory::getPaltoDirectory();

        return self::asUser("cp -n $paltoDirectory/structure/" . Palto::PARSE_CATEGORIES_SCRIPT . " $rootDirectory/"
            . " && cp -n $paltoDirectory/structure/" . Palto::PARSE_ADS_SCRIPT . " $rootDirectory/");
    }

    public static function safeLinkMigrations(): string
    {
        $rootDirectory = Directory::getRootDirectory();
        $paltoDirectory = Directory::getPaltoDirectory();

        return self::asUser("ln -s $paltoDirectory/db $rootDirectory/");
    }

    public static function safeCreateLogs(): string
    {
        $rootDirectory = Directory::getRootDirectory();

        return self::asUser("mkdir $rootDirectory/logs");
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

            if ($hasComment && $command == Cli::ignoreMac()) {
                Logger::warning(`$command`);
            } elseif ($command != Cli::ignoreMac()) {
//                Logger::debug($command);
                Logger::debug(`$command`);
            }
        }
    }

    public static function updatePermissions(string $path): string
    {
        return "chown -R \"km\" $path";
    }

    private static function getPhpVersion(): string
    {
        $output = `php -v`;
        $outputLines = explode(' ', $output);
        $versionParts = explode('.', $outputLines[1]);

        return $versionParts[0] . '.' . $versionParts[1];
    }

    private static function isLinux(): bool
    {
        return PHP_OS == 'Linux';
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