<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class WithoutComposer2 extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $hasComposerStructure = is_dir(\Palto\Directory::getRootDirectory() . '/vendor/haspadar/palto');
        if ($hasComposerStructure) {
            $wwwDirectory = dirname(\Palto\Directory::getRootDirectory());
            $projectNewName = \Palto\Directory::getProjectName() . '_without_composer';
            $projectNewTmpName = \Palto\Directory::getProjectName() . '_with_composer';
            $projectNewTmpDirectory = $wwwDirectory . '/' . $projectNewTmpName;
            $projectNewDirectory = $wwwDirectory . '/' . $projectNewName;
            $projectOldDirectory = \Palto\Directory::getRootDirectory();
            \Palto\Cli::runCommands([
                "cd $wwwDirectory && git clone https://github.com/haspadar/palto.git $projectNewName && cd $projectNewName && git checkout without_composer && composer update",
                "cp -R $projectOldDirectory/backups/* $projectNewDirectory/backups",
                "cp -R $projectOldDirectory/layouts/* $projectNewDirectory/layouts",
                "cp -R $projectOldDirectory/logs/* $projectNewDirectory/logs",
                "cp -R $projectOldDirectory/public/css $projectNewDirectory/public/",
                "cp -R $projectOldDirectory/public/img $projectNewDirectory/public/",
                "cp -R $projectOldDirectory/public/*.xml $projectNewDirectory/public/",
                "cp -R $projectOldDirectory/public/*.html $projectNewDirectory/public/",
                "cp -R $projectOldDirectory/public/sitemaps $projectNewDirectory/public/",
                "cp -R $projectOldDirectory/.env $projectNewDirectory/",
                "cp -R $projectOldDirectory/.htpasswd $projectNewDirectory/",
                "cp -R $projectOldDirectory/parse_categories.php $projectNewDirectory/",
                "cp -R $projectOldDirectory/parse_ads.php $projectNewDirectory/",
                "cp -R $projectOldDirectory/phinx.php $projectNewDirectory/",
            ]);
            file_put_contents("$projectOldDirectory/phinx.php", strtr("$projectOldDirectory/phinx.php", [
                'vendor/haspadar/palto' => '%%PHINX_CONFIG_DIR%%'
            ]));
            \Palto\Cli::runCommands([
                "mv $projectOldDirectory $projectNewTmpDirectory",
                "mv $projectNewDirectory $projectOldDirectory"
            ]);
        }
    }
}
