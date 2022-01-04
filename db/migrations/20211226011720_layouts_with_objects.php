<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class LayoutsWithObjects extends AbstractMigration
{
    public function change(): void
    {
        try {
            $this->execute('ALTER TABLE `ads`
ADD `category_level_3_id` int(11) unsigned NULL AFTER `category_id`,
ADD `category_level_2_id` int(11) unsigned NULL AFTER `category_level_3_id`,
ADD `category_level_1_id` int(11) unsigned NULL AFTER `category_level_2_id`,
ADD FOREIGN KEY (`category_level_3_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
ADD FOREIGN KEY (`category_level_2_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
ADD FOREIGN KEY (`category_level_1_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;');

            $this->execute('UPDATE ads AS a INNER JOIN categories AS c ON a.category_id=c.id LEFT JOIN categories AS c2 ON c.parent_id = c2.id SET a.category_level_3_id=a.category_id, a.category_level_2_id=c.parent_id, a.category_level_1_id=c2.parent_id WHERE c.level=3');
            $this->execute('UPDATE ads AS a INNER JOIN categories AS c ON a.category_id=c.id SET a.category_level_3_id=NULL, a.category_level_2_id=a.category_id, a.category_level_1_id=c.parent_id WHERE c.level=2');
            $this->execute('UPDATE ads AS a INNER JOIN categories AS c ON a.category_id=c.id SET a.category_level_3_id=NULL, a.category_level_2_id=NULL, a.category_level_1_id=a.category_id WHERE c.level=1');

            $this->execute('ALTER TABLE `ads`
ADD `region_level_2_id` int(11) unsigned NULL AFTER `region_id`,
ADD `region_level_1_id` int(11) unsigned NULL AFTER `region_level_2_id`,
ADD FOREIGN KEY (`region_level_2_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE,
ADD FOREIGN KEY (`region_level_1_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE;');
            $this->execute('UPDATE ads AS a INNER JOIN regions AS r ON a.region_id=r.id SET a.region_level_2_id=a.region_id, a.region_level_1_id = r.parent_id WHERE r.level=2');
            $this->execute('UPDATE ads AS a INNER JOIN regions AS r ON a.region_id=r.id SET a.region_level_2_id=NULL, a.region_level_1_id = a.region_id WHERE r.level=1');
        } catch (Exception $e) {
            \Palto\Logger::error($e->getTraceAsString());
        }
    }
}
