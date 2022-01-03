<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class RegionLevel3 extends AbstractMigration
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
        $this->execute('ALTER TABLE `ads`
ADD `region_level_3_id` int(11) unsigned NULL AFTER `region_level_2_id`,
ADD FOREIGN KEY (`region_level_3_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE;');
        
        $this->execute('UPDATE ads AS a INNER JOIN regions AS r ON a.region_id=r.id LEFT JOIN regions AS r2 ON r.parent_id = r2.id SET a.region_level_3_id=a.region_id, a.region_level_2_id=r.parent_id, a.region_level_1_id=r2.parent_id WHERE r.level=3');
        $this->execute('UPDATE ads AS a INNER JOIN regions AS r ON a.region_id=r.id SET a.region_level_3_id=NULL, a.region_level_2_id=a.region_id, a.region_level_1_id=r.parent_id WHERE r.level=2');
        $this->execute('UPDATE ads AS a INNER JOIN regions AS r ON a.region_id=r.id SET a.region_level_3_id=NULL, a.region_level_2_id=NULL, a.region_level_1_id=a.region_id WHERE r.level=1');

        $this->execute('UPDATE ads AS a INNER JOIN regions AS r ON a.region_id=r.id SET a.region_level_2_id=a.region_id, a.region_level_1_id = r.parent_id WHERE r.level=2');
        $this->execute('UPDATE ads AS a INNER JOIN regions AS r ON a.region_id=r.id SET a.region_level_2_id=NULL, a.region_level_1_id = a.region_id WHERE r.level=1');

//        $this->execute('UPDATE ads AS a INNER JOIN categories AS c ON a.category_id=c.id SET a.category_level_2_id=a.category_id, a.category_level_1_id = c.parent_id WHERE c.level=2');
//        $this->execute('UPDATE ads AS a INNER JOIN categories AS c ON a.category_id=r.id SET a.category_level_2_id=NULL, a.category_level_1_id = a.category_id WHERE c.level=1');
    }
}
