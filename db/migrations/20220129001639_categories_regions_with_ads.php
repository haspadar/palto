<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CategoriesRegionsWithAds extends AbstractMigration
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
        $this->execute("CREATE TABLE `categories_regions_with_ads` (
  `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `category_id` int(11) unsigned NULL,
  `region_id` int(11) unsigned NULL,
  FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE
);");
        $this->execute("ALTER TABLE `categories_regions_with_ads` ADD UNIQUE `category_id_region_id` (`category_id`, `region_id`);");

        $categories = $this->fetchAll("select * from categories");
        $regions = $this->fetchAll("select * from regions");
        foreach ($categories as $category) {
            if ($regions) {
                foreach ($regions as $region) {
                    if ($this->fetchAll("select * from ads WHERE category_level_"
                        . $category['level']
                        . "_id = "
                        . $category['id']
                        . ' AND region_level_'
                        . $region['level']
                        . "_id = "
                        . $region['id']
                        . " LIMIT 1"
                    )) {
                        $this->execute('INSERT IGNORE INTO categories_regions_with_ads(category_id, region_id) VALUES ('
                            . $category['id']
                            . ', '
                            . $region['id']
                            . ')'
                        );
                    }
                }
            } else {
                $this->execute('INSERT IGNORE INTO categories_regions_with_ads(category_id) VALUES ('
                    . $category['id']
                    . ')'
                );
            }
        }
    }
}
