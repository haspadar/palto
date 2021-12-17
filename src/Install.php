<?php

namespace Palto;

use Bramus\Monolog\Formatter\ColoredLineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Install
{
    private string $projectPath;
    private string $projectName;
    private string $databaseName;
    private string $databasePassword;
    private string $paltoPath;
    private string $configsPath;
    private Logger $logger;
    private bool $isUpdateOnly;

    public function __construct(string $projectPath = '', bool $isUpdateOnly = false)
    {
        $this->isUpdateOnly = $isUpdateOnly;
        $this->projectPath = $projectPath ?: trim(`pwd`);
        $this->paltoPath = $this->projectPath . '/vendor/haspadar/palto';
        $this->configsPath = $this->paltoPath . '/configs';
        $pathParts = explode('/', $this->projectPath);
        $this->projectName = $pathParts[count($pathParts) - 1];
        $this->logger = new Logger('install');
        $handler = new StreamHandler('php://stdout');
        $handler->setFormatter(new ColoredLineFormatter());
        $this->logger->pushHandler($handler);
        if ($this->isUpdateOnly) {
            $this->logger->warning('Extracting DB credentials from ' . $this->projectPath . '/.env');
            $databaseCredentials = Palto::extractDatabaseCredentials(
                file_get_contents($this->projectPath . '/.env')
            );
            $this->databaseName = $databaseCredentials['DB_NAME'];
            $this->databasePassword = $databaseCredentials['DB_PASSWORD'];
        } else {
            $this->databaseName = str_replace('.', '_', $this->projectName);
            $this->databasePassword = $this->generatePassword(12);
        }
    }

    public function run()
    {
        $this->runCommands(array_merge($this->getOSCommands(), $this->getLocalCommands()));
        $this->updateCron();
        $this->updateHost();
        $this->updateEnvOptions();
        $this->updateProjectConfigs();
        $this->updateSphinx();
        $this->updatePermissions();
        $this->showWelcome();
    }

    public function updateProjectConfigs()
    {
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
        $this->logger->info('Updating adminer');
        $projectPath = $this->projectPath;

        return "wget -O $projectPath/public/adminer.php https://www.adminer.org/latest-mysql-en.php";
    }

    private function getLocalCommands(): array
    {
        $projectPath = $this->projectPath;
        $paltoPath = $this->paltoPath;
        $databaseName = $this->databaseName;
        if ($this->isUpdateOnly) {
            return $this->getCopyStructureCommands();
        } else {
            return array_merge([
                "mkdir $projectPath/public"
            ],
                $this->getCopyStructureCommands(), [
                    "ln -s $paltoPath/structure/public/js $projectPath/public/",
                    "ln -s $paltoPath/structure/public/moderate $projectPath/public/",
                    "ln -s $paltoPath/structure/public/*.php $projectPath/public/",
                    "ln -s $paltoPath/structure/" . Sitemap::GENERATE_SCRIPT . " $projectPath/",
                    "ln -s $paltoPath/tasks $projectPath/",
                    "cp -n $paltoPath/structure/" . Palto::PARSE_CATEGORIES_SCRIPT . " $projectPath/",
                    "cp -n $paltoPath/structure/" . Palto::PARSE_ADS_SCRIPT . " $projectPath/",
                    "ln -s $paltoPath/db $projectPath/",
                    'mysql -e "' . $this->getMySqlSystemQuery() . '"',
                    "mysql $databaseName < $paltoPath" . '/db/palto.sql',
                    "mkdir $projectPath/logs"
                ]
            );
        }
    }

    private function getCopyStructureCommands(): array
    {
        $projectPath = $this->projectPath;
        $paltoPath = $this->paltoPath;

        return [
            "cp -R -n $paltoPath/structure/layouts $projectPath",
            "cp -R $paltoPath/structure/public/css $projectPath/public/",
            "cp -R $paltoPath/structure/public/img $projectPath/public/",
            "cp $paltoPath/structure/" . Palto::SHOW_ERRORS_SCRIPT . " $projectPath/",
            "cp $paltoPath/structure/" . Palto::ROUTES_SCRIPT . " $projectPath/",
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

    private function runCommands(array $commands)
    {
        foreach ($commands as $command) {
            $this->logger->debug('Running command: ' . $command);
            $this->logger->debug(`$command`);
        }
    }

    private function getLinuxLastPhpVersion(): string
    {
        $output = `php -v`;
        $outputLines = explode(' ', $output);
        $versionParts = explode('.', $outputLines[1]);

        return $versionParts[0] . '.' . $versionParts[1];
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
            $this->logger->info('Run command "php -S localhost:8000 -t public/" and open http://localhost:8000/adminer.php');
        } elseif ($this->isLinux()) {
            $this->logger->info('Open https://' . $this->projectName . '/adminer.php');
        }
    }

    private function getOSCommands(): array
    {
        if ($this->isMac()) {
            $commands = [
                'brew install sphinx'
            ];
        } elseif ($this->isLinux()) {
            $projectName = $this->projectName;
            $commands = [
                $this->getReplaceNginxMainConfigCommand(),
                $this->getReplaceNginxDomainConfigCommand(),
                "ln -s /etc/nginx/sites-available/$projectName /etc/nginx/sites-enabled/$projectName",
                $this->getReplaceNginxPhpFpmConfigCommand(),
                'service nginx restart'
            ];
        } else {
            $this->logger->critical('Your operating system ' . PHP_OS . ' is not supported');
            exit;
        }

        return $commands;
    }

    private function getMySqlSystemQuery(): string
    {
        $name = $this->databaseName;
        $password = $this->databasePassword;

        return implode(
            '', [
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

        return substr(str_shuffle($chars), 0, $length);
    }

    private function updateHost()
    {
        $this->logger->info('Updating host');
        $hostsFilePath = '/etc/hosts';
        $hostLine = '127.0.0.1 ' . $this->projectName;
        $hostsContent = file_get_contents($hostsFilePath);
        $isHostExists = mb_strpos($hostsContent, $hostLine) !== false;
        if (!$isHostExists) {
            file_put_contents($hostsFilePath, $hostsContent . PHP_EOL . $hostLine . PHP_EOL);
            $this->logger->debug('Added host "' . $hostLine . '"');
        } else {
            $this->logger->debug('host "' . $hostLine . '" already exists');
        }
    }

    private function updateCron()
    {
        $this->logger->info('Updating cron');
        $cronFilePath = '/etc/crontab';
        $commands = [
            '#Every minute' => [
                '* * * * * root cd ' . $this->projectPath . ' && vendor/bin/crunz schedule:run'
            ],
            '#Every 12 hours' => [
                '0 */12 * * * root cd ' . Sphinx::SPHINX_GLOBAL_DIRECTORY . ' && ' . Sphinx::REINDEX_COMMAND,
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
                    $this->logger->debug('Added cron command "' . $command . '"');
                    $this->runCommands(['service cron reload']);
                } else {
                    $this->logger->debug('cron command "' . $command . '" already exists');
                }
            }
        }
    }

    private function updateEnvOptions()
    {
        $this->logger->info('Updating env options');
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
        $this->logger->info('Updating Phinx');
        file_put_contents(
            $this->projectPath . '/' . Palto::PHINX_CONFIG,
            strtr(file_get_contents($this->configsPath . '/' . Palto::PHINX_CONFIG), [
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
        if ($this->isUpdateOnly
            && file_exists("/etc/nginx/sites-available/$projectName")
            && mb_strpos($nginxDomainConfig, 'AUTH=1') !== false
        ) {
            $nginxDomainConfig = str_replace('AUTH=1', 'AUTH=0', $nginxDomainConfig);
        }

        if ($this->isUpdateOnly
            && file_exists("/etc/nginx/sites-available/$projectName")
            && mb_strpos($nginxDomainConfig, 'set $no_cache 0;#disable cache') !== false
        ) {
            $nginxDomainConfig = str_replace(
                'set $no_cache 0;#disable cache',
                'set $no_cache 1;#disable cache',
                $nginxDomainConfig
            );
        }

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

    private function updatePermissions()
    {
        $this->runCommands(['`sudo chown -R "km":www-data /var/www/`;']);

    }

    private function updateSphinx()
    {
        $this->logger->info('Installing Sphinx config');
        (new Sphinx())->install('/var/www/');
    }
}