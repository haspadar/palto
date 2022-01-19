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

    public static function withErrors(): bool
    {
        return ($_GET['errors'] ?? 0);
    }

    public static function isDebug(): bool
    {
        return $_GET['debug'] ?? 0;
    }

    public static function getDomainUrl(): string
    {
        return 'https://www.' . Directory::getProjectName();
    }

    public static function setEnv(string $name, string $value)
    {
        self::getEnv();
        self::$env[$name] = $value;
    }

    public static function getEnv(): array
    {
        if (!isset(self::$env)) {
            self::$env = array_merge(
                Dotenv::createImmutable(Directory::getConfigsDirectory(), '.env')->load(),
                Dotenv::createImmutable(Directory::getConfigsDirectory(), '.pylesos')->load()
            );
        }

        return self::$env;
    }

    public static function getNginxDomainFilename(): string
    {
        if (Cli::isLinux()) {
            return '/etc/nginx/sites-available/' . Directory::getProjectName();
        }

        return '';
    }
}