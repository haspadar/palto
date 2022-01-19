<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Translates extends AbstractMigration
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
        \Palto\Cli::runCommands([
            'mv ' . \Palto\Directory::getConfigsDirectory() . \Palto\Directory::getStructureDirectory() . '/',
            'mv ' . \Palto\Directory::getRootDirectory() . '/.env ' . \Palto\Directory::getConfigsDirectory(),
            'Copy Pylesos env' => \Palto\Cli::safeCopyPylesosEnv()
        ]);
        \Palto\Update::run();
    }
}
