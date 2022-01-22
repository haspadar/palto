<?php

namespace Palto;

class Install
{
    public static function run()
    {
        $databaseName = str_replace('.', '_', Directory::getProjectName());
        $databaseUsername = $databaseName;
        $databasePassword = Cli::generatePassword();
        Cli::runCommands([
            'Update Nginx' => Cli::updateNginx(),
            Cli::updateNginxDomain($databaseName),
            Cli::safeLinkNginxDomain(),
            'Update FPM' => Cli::updateNginxPhpFpm(),
            Cli::reloadNginx(),
            'Copy Parse Scripts' => Cli::safeCopyParseScripts(),
            'Copy Translates' => Cli::safeCopyTranslates(),
            'Copy Counters' => Cli::safeCopyCounters(),
            'Copy Layouts' => Cli::safeCopyLayouts(),
            'Copy CSS' => Cli::safeCopyCss(),
            'Copy Images' => Cli::safeCopyImg(),
            'Create Database' => Cli::createDatabase($databaseName, $databaseUsername, $databasePassword),
            'Add Cron' => Cli::safeAddCron(),
            'Generate Env' => Cli::generateEnv($databaseName, $databaseUsername, $databasePassword),
            'Generate Pylesos env' => Cli::safeCopyPylesosEnv(),
            'Update Phinx' => Cli::updatePhinx($databaseName, $databaseUsername, $databasePassword),
            'Update Htpasswd' => Cli::updateHtpasswd(),
            'Update Host' => Cli::updateHost(),
            'Update permissions' => Cli::updatePermissions(Directory::getRootDirectory()),
            Cli::updatePermissions('/etc/nginx/sites-available')
        ]);
    }
}