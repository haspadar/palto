<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CategoryLinks extends AbstractMigration
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
        $this->execute("DROP TABLE IF EXISTS category_links");
        $this->execute("CREATE TABLE `category_links` (
          `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `category_id` int(11) unsigned NULL,
          `child_id` int(11) unsigned NULL,
          FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
          FOREIGN KEY (`child_id`) REFERENCES `categories` (`id`)
        );");
        $this->execute("DROP TABLE IF EXISTS region_links");
        $this->execute("CREATE TABLE `region_links` (
          `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `region_id` int(11) unsigned NULL,
          `child_id` int(11) unsigned NULL,
          FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`),
          FOREIGN KEY (`child_id`) REFERENCES `regions` (`id`)
        );");
    }
}
