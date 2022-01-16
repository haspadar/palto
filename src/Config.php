<?php
namespace Palto;

use Dotenv\Dotenv;

class Config
{
    /**
     * @var null[]|string[]
     */
    private static array $env;

    public static function get(string $name): string
    {
        return self::getEnv()[$name] ?? '';
    }

    public static function isCacheEnabled(): bool
    {
        $name = Cli::getNginxDomainFilename();
        if ($name) {
            $nginxConfig = file_get_contents($name);

            return mb_strpos($nginxConfig, '#set $no_cache 1;#disable cache') === false;
        }

        return false;
    }

    public static function withErrors(): bool
    {
        return ($_GET['errors'] ?? 0);
    }

    public static function isDebug(): bool
    {
        return self::get('DEBUG') || ($_GET['debug'] ?? 0);
    }

    public static function getDomainUrl(): string
    {
        $domainUrl = self::get('DOMAIN_URL');
        if (!$domainUrl) {
            $domainUrl = 'https://www.' . Directory::getProjectName();
        }

        return $domainUrl;
    }

    public static function setEnv(string $name, string $value)
    {
        self::getEnv();
        self::$env[$name] = $value;
    }

    public static function getEnv(): array
    {
        if (!isset(self::$env)) {
            $dotenv = Dotenv::createImmutable(Directory::getRootDirectory());
            self::$env = $dotenv->load();
        }

        return self::$env;
    }
}