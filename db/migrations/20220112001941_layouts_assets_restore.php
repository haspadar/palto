<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class LayoutsAssetsRestore extends AbstractMigration
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
        $projectNewTmpName = \Palto\Directory::getProjectName() . '_with_composer';
        $projectDirectory = Palto\Directory::getRootDirectory();
        \Palto\Cli::runCommands([
            "cp -r $projectNewTmpName/layouts $projectDirectory",
            "cp -r $projectNewTmpName/public/css $projectDirectory/public/",
            "cp -r $projectNewTmpName/public/img $projectDirectory/public/",
        ]);
    }
}
