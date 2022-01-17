<?php
declare(strict_types=1);

use Palto\Backup;
use Palto\Update;
use Phinx\Migration\AbstractMigration;

final class Karman extends AbstractMigration
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
        Backup::createArchive();
        $replaces = [
            'layouts/*' => [
                '\Palto\Layout' => '\Palto\Layout\Client'
            ]
        ];
        Update::replaceCode($replaces);
        \Palto\Cli::runCommands([
            'mkdir -p ' . \Palto\Directory::getLayoutsDirectory() . '/client/static',
            'mv ' . \Palto\Directory::getLayoutsDirectory() . '/*.php ' . \Palto\Directory::getLayoutsDirectory() . '/client/',
            'mv ' . \Palto\Directory::getLayoutsDirectory() . '/client/*-list.php ' . \Palto\Directory::getLayoutsDirectory() . '/client/static/',
            'mv ' . \Palto\Directory::getLayoutsDirectory() . '/client/index.php ' . \Palto\Directory::getLayoutsDirectory() . '/client/static/',
            'mv ' . \Palto\Directory::getLayoutsDirectory() . '/client/registration.php ' . \Palto\Directory::getLayoutsDirectory() . '/client/static/',
        ]);
    }
}
