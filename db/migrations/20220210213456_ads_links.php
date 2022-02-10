<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AdsLinks extends AbstractMigration
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
        $this->execute("CREATE TABLE `ads_links` (
          `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `ad_id` int(11) unsigned NULL,
          `category_level` int unsigned NOT NULL DEFAULT '0',
          `category_id` int(11) unsigned NULL,
          `region_level` int unsigned NOT NULL DEFAULT '0',
          `region_id` int(11) unsigned NULL,
          FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`),
          FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
          FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`)
        );");
    }
}
