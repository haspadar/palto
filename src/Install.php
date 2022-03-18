<?php

namespace Palto;

class Install
{
    public static function run($databaseUsername = '', $databasePassword = '')
    {
        if (!$databaseUsername && !$databasePassword) {
            $databaseUsername = str_replace('.', '_', Directory::getProjectName());
            $databasePassword = Cli::generatePassword();
        }

        $databaseName = $databaseUsername;
        Cli::runCommands([
            'Update Nginx' => Cli::updateNginx(),
            Cli::updateNginxDomain($databaseName),
            Cli::safeLinkNginxDomain(),
            'Update FPM' => Cli::updateNginxPhpFpm(),
            Cli::reloadNginx(),
//            'Copy Parse Scripts' => Cli::safeCopyParseScripts(),
            'Copy Translates' => Cli::safeCopyTranslates(),
            'Copy Counters' => Cli::safeCopyCounters(),
            'Copy CSS' => Cli::safeCopyCss(),
            'Create Database' => Cli::createDatabase($databaseName, $databaseUsername, $databasePassword),
            'Add Cron' => Cli::safeAddCron(),
            'Generate general env' => Cli::generateGeneralEnv($databaseName, $databaseUsername, $databasePassword),
            'Copy pylesos env' => Cli::safeCopyPylesosEnv(),
            'Copy layouts env' => Cli::safeCopyLayoutsEnv(),
            'Update Phinx' => Cli::updatePhinx($databaseName, $databaseUsername, $databasePassword),
            'Update Htpasswd' => Cli::updateHtpasswd(),
            'Update Host' => Cli::updateHost(),
            'Update permissions' => Cli::updatePermissions(Directory::getRootDirectory()),
            Cli::updatePermissions('/etc/nginx/sites-available', 'www-data'),
        ]);
    }
}