<?php
namespace Palto;

class Install
{
    private string $projectPath;

    private string $projectName;

    public function __construct()
    {
        $this->projectPath = trim(`pwd`);
        $pathParts = explode('/', $this->projectPath);
        $this->projectName = $pathParts[count($pathParts) - 1];
        $this->mysqlPassword = $this->generatePassword(12);
        var_dump($this->mysqlPassword);exit;
    }

    public function run()
    {
        $osCommands = $this->getOSCommands();
        if ($osCommands) {
            $path = $this->projectPath;
            $envConfig = $this->getEnvConfig();
            $this->runCommands(
                array_merge([
                    "cp -R $path/vendor/haspadar/palto/examples/* ./",
                    "wget -O $path/public/adminer.php https://www.adminer.org/latest-mysql-en.php"
                ], $osCommands, [
                    'mysql -e "' . $this->getMySqlQuery() . '"',
                    "cat $envConfig > $path/.env"
                ])
            );
            $this->showWelcome();
        }
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
        return "upstream php$phpMajorVersion-fpm{
    server unix:/run/php/php$phpMinorVersion-fpm.sock;
}";
    }

    private function getNginxConfig(string $phpMajorVersion): string
    {
        $projectName = $this->projectName;
        $path = $this->projectPath;

        return sprintf('server {
	listen 80;
	listen [::]:80;
	root %s;
	index index.php index.html;
	server_name %s;
	location ~ \.php$ {
          try_files $uri = 404;
          include fastcgi_params;
          fastcgi_pass php%s-fpm;
          fastcgi_index index.php;
          fastcgi_intercept_errors on;
          fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        }
 
	location / {
          try_files $uri $uri/ @rewrite;
          expires max;
	}
      location @rewrite {
          rewrite ^/(.*)$ /index.php;
      }
}
',
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
        $projectName = $this->projectName;
        if ($this->isMac()) {
            $commands = [
                'brew install mariadb',
            ];
        } elseif ($this->isLinux()) {
            $phpMinorVersion = $this->getLinuxLastPhpVersion();
            $phpMajorVersion = intval($phpMinorVersion);
            $phpFullVersion = 'php' . $phpMinorVersion;
            $nginxConfig = $this->getNginxConfig($phpMajorVersion);
            $nginxPhpConfig = $this->getNginxPhpConfig($phpMajorVersion, $phpMinorVersion);
            $commands = [
                'apt-get install mariadb-server',
                'apt-get install nginx',
                "apt install $phpFullVersion-fpm $phpFullVersion-cli $phpFullVersion-mysql $phpFullVersion-xml $phpFullVersion-curl $phpFullVersion-zip $phpFullVersion-iconv",
                "echo $nginxConfig > /etc/nginx/sites-available/$projectName",
                "ln -s /etc/nginx/sites-available/$projectName /etc/nginx/sites-enabled/$projectName",
                "echo $nginxPhpConfig > /etc/nginx/conf.d/php$phpMajorVersion-fpm.conf",
                'service nginx reload'
            ];
        } else {
            $this->log('Your operating system ' . PHP_OS . ' is not supported');
            $commands  = [];
        }

        return $commands;
    }

    private function getMySqlQuery(): string
    {
        $projectName = $this->projectName;
        $password = $this->mysqlPassword;

        return implode(
            '',[
                "CREATE DATABASE $projectName CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;",
                "CREATE USER '$projectName'@'localhost' IDENTIFIED BY '$password';",
                "GRANT ALL PRIVILEGES ON *.* TO '$projectName'@'localhost';",
                "USE $projectName;",
                "CREATE TABLE `regions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL DEFAULT '',
  `parent_id` int(10) unsigned DEFAULT NULL,
  `url` varchar(200) NOT NULL DEFAULT '',
  `level` int(10) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `url` (`url`),
  KEY `title` (`title`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `regions_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `regions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
                "CREATE TABLE `categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned DEFAULT NULL,
  `level` int(11) unsigned NOT NULL DEFAULT 1,
  `title` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `donor_url` varchar(500) NOT NULL DEFAULT '',
  `icon_url` varchar(1024) NOT NULL DEFAULT '',
  `icon_text` text DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `donor_url` (`donor_url`),
  KEY `title` (`title`),
  KEY `parent_id` (`parent_id`),
  KEY `url` (`url`),
  KEY `level` (`level`),
  CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
                "CREATE TABLE `ads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(600) NOT NULL DEFAULT '',
  `category_id` int(11) unsigned DEFAULT NULL,
  `region_id` int(10) unsigned DEFAULT NULL,
  `title` varchar(1000) NOT NULL DEFAULT '',
  `text` text DEFAULT NULL,
  `address` varchar(1000) NOT NULL DEFAULT '',
  `coordinates` varchar(1000) NOT NULL DEFAULT '',
  `post_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`),
  KEY `category_id` (`category_id`),
  KEY `region_id` (`region_id`),
  CONSTRAINT `ads_ibfk_3` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `ads_ibfk_4` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
                "CREATE TABLE `ads_images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ad_id` int(11) DEFAULT NULL,
  `big` varchar(1000) NOT NULL DEFAULT '',
  `small` varchar(1000) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `ad_id` (`ad_id`),
  CONSTRAINT `ads_images_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
            ]
        );
    }

    private function generatePassword(int $length): string
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

        return substr(str_shuffle($chars),0, $length);
    }

    private function getEnvConfig(): string
    {
        $config = file_get_contents($this->projectPath . '/vendor/haspadar/palto/.env.example');

        return strtr($config, [
            'DB_USER=' => 'DB_USER=' . $this->projectName,
            'DB_PASSWORD=' => 'DB_PASSWORD=' . $this->mysqlPassword,
            'DB_NAME=' => 'DB_NAME=' . $this->projectName,
            'DEBUG=0' => 'DEBUG=1'
        ]);
    }
}