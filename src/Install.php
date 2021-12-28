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
            Cli::restartNginx(),
            Cli::safeCreatePublicDirectory(),
            'Copy Layouts' => Cli::safeCopyLayouts(),
            'Copy CSS' => Cli::safeCopyCss(),
            'Copy Images' => Cli::safeCopyImg(),
            'Link JS' => Cli::safeLinkJs(),
            'Link Routes' => Cli::safeLinkRoutes(),
            'Link Moderate' => Cli::safeLinkModerate(),
            'Link Php Scripts' => Cli::safeLinkPublicPhpScripts(),
            'Link Sitemap Script' => Cli::safeLinkSitemapScript(),
            'Copy Crunz' => Cli::copyCrunz(),
            Cli::safeLinkCrunzTasks(),
            'Copy Parse Scripts' => Cli::safeCopyParseScripts(),
            'Link Migrations' => Cli::safeLinkMigrations(),
            'Create Database' => Cli::createDatabase($databaseName, $databaseUsername, $databasePassword),
            Cli::safeCreateLogs(),
            'Add Cron' => Cli::safeAddCron(),
            'Generate Env' => Cli::generateEnv($databaseName, $databaseUsername, $databasePassword),
            'Update Phinx' => Cli::updatePhinx($databaseName, $databaseUsername, $databasePassword),
            'Download Adminer' => Cli::downloadAdminer(),
            'Update Htpasswd' => Cli::updateHtpasswd(),
        ]);
    }
}