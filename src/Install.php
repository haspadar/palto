<?php
namespace Palto;

class Install
{
    private string $projectPath;
    private string $projectName;
    private string $databaseName;
    private string $databasePassword;
    private string $paltoPath;
    private string $configsPath;

    public function __construct()
    {
        $this->projectPath = trim(`pwd`);
        $this->paltoPath = $this->projectPath . '/vendor/haspadar/palto';
        $this->configsPath = $this->paltoPath . '/configs';
        $pathParts = explode('/', $this->projectPath);
        $this->projectName = $pathParts[count($pathParts) - 1];
        $this->databaseName = str_replace('.', '_', $this->projectName);
        $this->databasePassword = $this->generatePassword(12);
    }

    public function run()
    {
        $this->runCommands(array_merge($this->getOSCommands(), $this->getLocalCommands()));
        (new Sphinx())->install('/var/www/');
        $this->updateCron();
        $this->updateHost();
        $this->updateSystemConfigs();
        $this->updateProjectConfigs();
        $this->showWelcome();
    }

    public function updateSystemConfigs()
    {
//        $this->updateSphinx();
        $this->runCommands([
            $this->getReplaceNginxMainConfigCommand(),
            $this->getReplaceNginxPhpFpmConfigCommand()
        ]);
    }

    public function updateProjectConfigs()
    {
        $this->updateEnvOptions();
        $this->updatePhinx();
        $this->runCommands([
            $this->getReplaceCrunzCommand(),
            $this->getReplaceNginxMainConfigCommand(),
            $this->getReplaceNginxDomainConfigCommand(),
            $this->getReplaceNginxPhpFpmConfigCommand(),
            $this->getReplaceLastAdminerCommand(),
            $this->getReplaceHtpasswdCommand()
        ]);
    }

    private function getReplaceCrunzCommand(): string
    {
        $projectPath = $this->projectPath;
        $configsPath = $this->configsPath;

        return "cp $configsPath/crunz.yml" . " $projectPath/";
    }

    private function getReplaceLastAdminerCommand(): string
    {
        $this->log('Updating adminer');
        $projectPath = $this->projectPath;

        return "wget -O $projectPath/public/adminer.php https://www.adminer.org/latest-mysql-en.php";
    }

    private function getLocalCommands(): array
    {
        $projectPath = $this->projectPath;
        $paltoPath = $this->paltoPath;
        $databaseName = $this->databaseName;

        return [
            "cp -R $paltoPath/structure/layouts $projectPath/",
//            "cp -R -n $paltoPath/sphinx /var/www/",
//            "cp -R -n $paltoPath/structure/sphinx $projectPath",
            "mkdir $projectPath/public",
            "cp -R $paltoPath/structure/public/css $projectPath/public/",
            "cp -R $paltoPath/structure/public/img $projectPath/public/",
            "ln -s $paltoPath/structure/public/js $projectPath/public/",
            "ln -s $paltoPath/structure/public/moderate $projectPath/public/",
            "ln -s $paltoPath/structure/public/*.php $projectPath/public/",
            "ln -s $paltoPath/structure/" . Sitemap::GENERATE_SCRIPT . " $projectPath/",
            "ln -s $paltoPath/tasks $projectPath/",
            "cp $paltoPath/structure/" . Palto::PARSE_CATEGORIES_SCRIPT . " $projectPath/",
            "cp $paltoPath/structure/" . Palto::PARSE_ADS_SCRIPT . " $projectPath/",
            "cp $paltoPath/structure/" . Palto::SHOW_ERRORS_SCRIPT . " $projectPath/",
            "ln -s $paltoPath/db $projectPath/",
            'mysql -e "' . $this->getMySqlSystemQuery() . '"',
            "mysql $databaseName < $paltoPath" . '/db/palto.sql',
            "mkdir $projectPath/logs"
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
        echo '[' . (new \DateTime())->format('H:i:s') . ']' . $string . PHP_EOL;
    }

    private function runCommands(array $commands)
    {
        foreach ($commands as $command) {
            $this->log('Running command: ' . $command);
            $this->log(`$command`);
        }
    }

    private function getLinuxLastPhpVersion(): string
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

    private function getNginxPhpFpmConfig(string $phpMajorVersion, string $phpMinorVersion): string
    {
        return sprintf(
            file_get_contents($this->configsPath . '/nginx/php-fpm.conf'),
            $phpMajorVersion,
            $phpMinorVersion
        );
    }

    private function getNginxMainConfig(): string
    {
        return file_get_contents($this->configsPath . '/nginx/nginx.conf');
    }

    private function getNginxDomainConfig(string $phpMajorVersion): string
    {
        $projectName = $this->projectName;
        $path = $this->projectPath;

        return sprintf(
            file_get_contents($this->configsPath . '/nginx/domain'),
            $path,
            "$projectName *.$projectName",
            $phpMajorVersion,
            $this->databaseName,
            $this->databaseName,
            $projectName
        );
    }

    private function showWelcome()
    {
        if ($this->isMac()) {
            $this->log('Run command "php -S localhost:8000 -t public/" and open http://localhost:8000/adminer.php');
        } elseif ($this->isLinux()) {
            $this->log('Open https://' . $this->projectName . '/adminer.php');
        }
    }

    private function getOSCommands(): array
    {
        if ($this->isMac()) {
            $commands = [
                'brew install mariadb',
                'brew install sphinx'
            ];
        } elseif ($this->isLinux()) {
            $projectName = $this->projectName;
            $phpMinorVersion = $this->getLinuxLastPhpVersion();
            $phpFullVersion = 'php' . $phpMinorVersion;
            $commands = [
                'apt-get install mariadb-server',
                'apt-get install nginx',
                "apt install $phpFullVersion-fpm $phpFullVersion-cli $phpFullVersion-mysql $phpFullVersion-xml $phpFullVersion-curl $phpFullVersion-zip $phpFullVersion-iconv",
                'apt-get install sphinxsearch',
                $this->getReplaceNginxMainConfigCommand(),
                $this->getReplaceNginxDomainConfigCommand(),
                "ln -s /etc/nginx/sites-available/$projectName /etc/nginx/sites-enabled/$projectName",
                $this->getReplaceNginxPhpFpmConfigCommand(),
                'service nginx restart'
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

    private function updateHost()
    {
        $hostsFilePath = '/etc/hosts';
        $hostLine = '127.0.0.1 ' . $this->projectName;
        $hostsContent = file_get_contents($hostsFilePath);
        $isHostExists = mb_strpos($hostsContent, $hostLine) !== false;
        if (!$isHostExists) {
            file_put_contents($hostsFilePath, $hostsContent . PHP_EOL . $hostLine . PHP_EOL);
            $this->log('Added host "' . $hostLine . '"');
        } else {
            $this->log('host "' . $hostLine . '" already exists');
        }
    }

    private function updateCron()
    {
        $cronFilePath = '/etc/crontab';
        $commands = [
            '#Every minute' => [
                '* * * * * root cd ' . $this->projectPath . ' && vendor/bin/crunz schedule:run'

            ]
        ];
        foreach ($commands as $comment => $commentCommands) {
            foreach ($commentCommands as $command) {
                $cronFileContent = file_get_contents($cronFilePath);
                $isCommandExists = mb_strpos($cronFileContent, $command) !== false;
                if (!$isCommandExists) {
                    file_put_contents(
                        $cronFilePath,
                        $cronFileContent . PHP_EOL . $comment . PHP_EOL . $command . PHP_EOL
                    );
                    $this->log('Added cron command "' . $command . '"');
                    $this->runCommands(['service cron reload']);
                } else {
                    $this->log('cron command "' . $command . '" already exists');
                }
            }
        }
    }

//    private function updateSphinx()
//    {
//        $this->log('Updating sphinx config');
//        $sphinx = new Sphinx();
//        $sphinx->addConfig(
//            $this->configsPath . '/' . Sphinx::SPHINX_LOCAL_CONFIG,
//            $this->databaseName,
//            $this->databasePassword
//        );
//    }

    private function updateEnvOptions()
    {
        $this->log('Updating env options');
        file_put_contents(
            $this->projectPath . '/.env',
            strtr(file_get_contents($this->configsPath . '/.env'), [
                'DB_USER=' => 'DB_USER=' . $this->databaseName,
                'DB_PASSWORD=' => 'DB_PASSWORD=' . $this->databasePassword,
                'DB_NAME=' => 'DB_NAME=' . $this->databaseName,
            ])
        );
    }

    private function updatePhinx()
    {
        $this->log('Updating Phinx');
        file_put_contents(
            $this->projectPath . '/' . Palto::PHINX_CONFIG,
            strtr(file_get_contents($this->configsPath . Palto::PHINX_CONFIG), [
                'production_db' => $this->databaseName,
                'production_user' => $this->databaseName,
                'production_pass' => $this->databasePassword
            ])
        );
    }

    private function getManySitesDonorDirectory(): string
    {
        foreach ($this->getPaltoDirectories() as $paltoDirectory) {
            if ($paltoDirectory) {
                $manySitesAdsScript = $paltoDirectory . '/' . Palto::PARSE_ADS_SCRIPT;
                if (!is_link($manySitesAdsScript)) {
                    return $paltoDirectory;
                }
            }
        }

        return '';
    }

    private function getPaltoDirectories(): array
    {
        $directories = scandir($this->projectPath . '/..');
        $paltoDirectories = [];
        foreach ($directories as $directory) {
            $directoryPath = $this->projectPath . '/../' . $directory;
            if (!in_array($directory, ['..', '.']) && is_dir($directoryPath) && $this->isPaltoDirectory($directoryPath)) {
                $paltoDirectories[] = $directoryPath;
            }
        }

        return $paltoDirectories;
    }

    private function isPaltoDirectory(string $directoryPath): bool
    {
        return file_exists($directoryPath . '/' . Palto::PARSE_ADS_SCRIPT);
    }

    private function getReplaceNginxMainConfigCommand(): string
    {
        $nginxMainConfig = $this->getNginxMainConfig();

        return "echo '$nginxMainConfig' > /etc/nginx/nginx.conf";
    }

    private function getReplaceNginxDomainConfigCommand(): string
    {
        $phpMajorVersion = $this->getPhpMajorVersion();
        $nginxDomainConfig = $this->getNginxDomainConfig($phpMajorVersion);
        $projectName = $this->projectName;

        return "echo '$nginxDomainConfig' > /etc/nginx/sites-available/$projectName";
    }

    private function getReplaceNginxPhpFpmConfigCommand(): string
    {
        $phpMinorVersion = $this->getLinuxLastPhpVersion();
        $phpMajorVersion = $this->getPhpMajorVersion();
        $nginxPhpFpmConfig = $this->getNginxPhpFpmConfig($phpMajorVersion, $phpMinorVersion);

        return "echo '$nginxPhpFpmConfig' > /etc/nginx/conf.d/php$phpMajorVersion-fpm.conf";
    }

    /**
     * @return int
     */
    private function getPhpMajorVersion(): int
    {
        return intval($this->getLinuxLastPhpVersion());
    }

    private function getReplaceHtpasswdCommand(): string
    {
        $configsPath = $this->configsPath;
        $projectPath = $this->projectPath;

        return "cp -R $configsPath/.htpasswd $projectPath/";
    }
}