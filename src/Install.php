<?php

namespace Palto;

class Install
{
    public static function run($options)
    {
        if (!$options['database_username'] && !$options['database_password']) {
            $options['database_username'] = str_replace('.', '_', Directory::getProjectName());
            $options['database_password'] = Cli::generatePassword();
        }

        $databaseName = $options['database_username'];
        $commands = [
            'Update Nginx' => Cli::updateNginx(),
            Cli::updateNginxDomain($databaseName),
            Cli::safeLinkNginxDomain(),
            'Update FPM' => Cli::updateNginxPhpFpm(),
            Cli::reloadNginx(),
            'Copy Translates' => Cli::safeCopyTranslates(),
            'Copy Counters' => Cli::safeCopyCounters(),
            'Copy CSS' => Cli::safeCopyCss(),
            'Create Database' => Cli::createDatabase($databaseName, $options['database_username'], $options['database_password']),
            'Add Cron' => Cli::safeAddCron(),
            'Generate general env' => Cli::generateGeneralEnv($databaseName, $options['database_username'], $options['database_password']),
            'Copy pylesos env' => Cli::safeCopyPylesosEnv(),
            'Copy layouts env' => Cli::safeCopyLayoutsEnv(),
            'Update Phinx' => Cli::updatePhinx($databaseName, $options['database_username'], $options['database_password']),
            'Update Htpasswd' => Cli::updateHtpasswd(),
            'Update Host' => Cli::updateHost(),
            'Update permissions' => Cli::updatePermissions(Directory::getRootDirectory()),
            Cli::updatePermissions('/etc/nginx/sites-available', 'www-data'),
        ];
        Cli::runCommands($commands);
    }
}