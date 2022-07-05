<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AdPageUrlFix extends AbstractMigration
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
        $sql = 'UPDATE pages SET url=\'/([a-zA-Z0-9_-]+)/([a-zA-Z0-9_-]+)(/[a-zA-Z0-9_-]+)?(/[a-zA-Z0-9_-]+)?/ad(\d+)\' WHERE name=\'ad\'';
        echo $sql . PHP_EOL;
        $this->execute($sql);
        $this->execute('UPDATE pages SET url=\'/([a-zA-Z0-9_-]+)/([a-zA-Z0-9_-]+)(/[a-zA-Z0-9_-]+)?(/[a-zA-Z0-9_-]+)?(\d+)?\' WHERE url=\'/([a-zA-Z0-9_-]+)/([a-zA-Z0-9_-]+)(/[a-zA-Z0-9_-]+)?(/[a-zA-Z0-9_-]+)?(/d+)?\'');
        $this->execute('UPDATE pages SET url=\'/([a-zA-Z0-9_-]+)(\d+)?\' WHERE url=\'/([a-zA-Z0-9_-]+)(/d+)?\'');
    }
}
