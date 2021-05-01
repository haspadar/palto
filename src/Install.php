<?php
namespace Palto;

class Install
{
    private string $projectPath;
    private string $projectName;
    private string $databaseName;
    private string $databasePassword;
    private string $paltoPath;

    public function __construct()
    {
        $this->projectPath = trim(`pwd`);
        $this->paltoPath = $this->projectPath . '/vendor/haspadar/palto';
        $pathParts = explode('/', $this->projectPath);
        $this->projectName = $pathParts[count($pathParts) - 1];
        $this->databaseName = str_replace('.', '_', $this->projectName);
        $this->databasePassword = $this->generatePassword(12);
    }

    public function run()
    {
        $osCommands = $this->getOSCommands();
        $this->runCommands(array_merge($osCommands, $this->getLocalCommands()));
        $this->updateEnvOptions();
        $this->updateCron();
        $this->showWelcome();
    }

    private function getLocalCommands(): array
    {
        $projectPath = $this->projectPath;
        $paltoPath = $this->paltoPath;
        $databaseName = $this->databaseName;

        return [
            "cp -R $paltoPath/structure/* $projectPath/",
            "wget -O $projectPath/public/adminer.php https://www.adminer.org/latest-mysql-en.php",
            'mysql -e "' . $this->getMySqlSystemQuery() . '"',
            "mysql $databaseName < $paltoPath" . '/db/palto.sql',
            "cp $paltoPath/.env.example $projectPath/.env",
        ];
    }

    private function isLinux(): bool
    {
        return PHP_OS == 'Linux';
    }

    private function isMac(): bool
    {
        return PHP_OS == 'Darwin';
    }

    private function log($string)
    {
        echo $string . PHP_EOL;
    }

    private function runCommands(array $commands)
    {
        foreach ($commands as $command) {
            $this->log('Running command: ' . $command);
            $this->log(`$command`);
        }
    }

    private function getLinuxLastPhpVersion()
    {
        $output = `apt show php`;
        $outputLines = explode(PHP_EOL, $output);
        foreach ($outputLines as $outputLine) {
            $parts = explode(': ', $outputLine);
            if ($parts[0] == 'Version') {
                $version = $parts[1];
                if (strpos($version, ':') !== false) {
                    $version = explode(':', $version)[1];
                }

                if (strpos($version, '+') !== false) {
                    $version = explode('+', $version)[0];
                }

                return $version;
            }
        }

        return '7.4';
    }

    private function getNginxPhpConfig(string $phpMajorVersion, string $phpMinorVersion): string
    {
        return sprintf(
            file_get_contents($this->paltoPath . '/configs/nginx/php-fpm.conf'),
            $phpMajorVersion,
            $phpMinorVersion
        );
    }

    private function getNginxConfig(string $phpMajorVersion): string
    {
        $projectName = $this->projectName;
        $path = $this->projectPath;

        return sprintf(
            file_get_contents($this->paltoPath . '/configs/nginx/domain'),
            $path,
                   "$projectName *.$projectName",
                   $phpMajorVersion
        );
    }

    private function showWelcome()
    {
        if ($this->isMac()) {
            $this->log('Run command "php -S localhost:8000 -t public/" and open http://localhost:8000/adminer.php');
        } elseif ($this->isLinux()) {
            $this->log('Open http://' . $this->projectName . '/adminer.php');
        }
    }

    private function getOSCommands(): array
    {
        if ($this->isMac()) {
            $commands = [
                'brew install mariadb',
            ];
        } elseif ($this->isLinux()) {
            $projectName = $this->projectName;
            $phpMinorVersion = $this->getLinuxLastPhpVersion();
            $phpMajorVersion = intval($phpMinorVersion);
            $phpFullVersion = 'php' . $phpMinorVersion;
            $nginxConfig = $this->getNginxConfig($phpMajorVersion);
            $nginxPhpConfig = $this->getNginxPhpConfig($phpMajorVersion, $phpMinorVersion);
            $commands = [
                'apt-get install mariadb-server',
                'apt-get install nginx',
                "apt install $phpFullVersion-fpm $phpFullVersion-cli $phpFullVersion-mysql $phpFullVersion-xml $phpFullVersion-curl $phpFullVersion-zip $phpFullVersion-iconv",
                "echo '$nginxConfig' > /etc/nginx/sites-available/$projectName",
                "ln -s /etc/nginx/sites-available/$projectName /etc/nginx/sites-enabled/$projectName",
                "echo '$nginxPhpConfig' > /etc/nginx/conf.d/php$phpMajorVersion-fpm.conf",
                'service nginx reload'
            ];
        } else {
            $this->log('Your operating system ' . PHP_OS . ' is not supported');
            exit;
        }

        return $commands;
    }

    private function getMySqlSystemQuery(): string
    {
        $name = $this->databaseName;
        $password = $this->databasePassword;

        return implode(
            '',[
                  "DROP DATABASE IF EXISTS $name;",
                  "CREATE DATABASE $name CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;",
                  "DROP USER IF EXISTS '$name'@'localhost';",
                  "CREATE USER '$name'@'localhost' IDENTIFIED BY '$password';",
                  "GRANT ALL PRIVILEGES ON *.* TO '$name'@'localhost';"
              ]
        );
    }

    private function generatePassword(int $length): string
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

        return substr(str_shuffle($chars),0, $length);
    }

    private function updateCron()
    {
        $cronFilePath = '/etc/crontab';
        $everyHourCommands = [
            '0 * * * *  root cd ' . $this->projectPath . ' && php parse_ads.php'
        ];
        $cronFileContent = file_get_contents($cronFilePath);
        foreach ($everyHourCommands as $command) {
            $isCommandExists = mb_strpos($command, $cronFileContent) !== false;
            if (!$isCommandExists) {
                file_put_contents($cronFilePath, $cronFileContent . PHP_EOL . '#Every hour' . PHP_EOL . $command);
                $this->log('Added cron command "' . $command . '"');
                $this->runCommands(['service cron reload']);
            } else {
                $this->log('cron command "' . $command . '" already exists');
            }
        }
    }

    private function updateEnvOptions()
    {
        $this->log('Updating env options');
        file_put_contents(
            $this->projectPath . '/.env',
            strtr(file_get_contents($this->projectPath . '/.env'), [
                'DB_USER=' => 'DB_USER=' . $this->databaseName,
                'DB_PASSWORD=' => 'DB_PASSWORD=' . $this->databasePassword,
                'DB_NAME=' => 'DB_NAME=' . $this->databaseName,
            ])
        );
    }
}