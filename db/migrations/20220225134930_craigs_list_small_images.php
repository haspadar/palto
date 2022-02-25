<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CraigsListSmallImages extends AbstractMigration
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
        $this->execute("UPDATE ads_images SET small = REPLACE(big, '600x450.jpg', '50x50c.jpg') WHERE big <> '' AND small=''");
    }
}
