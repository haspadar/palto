<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CategoriesRegionsAdsCount extends AbstractMigration
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
        $this->log('Update 1');
        $this->execute("ALTER TABLE `categories_regions_with_ads` ADD `ads_count` int unsigned NOT NULL DEFAULT '0';");
        $this->execute("update categories_regions_with_ads as crwa
 inner join categories as c on crwa.category_id = c.id
 set crwa.ads_count = (select count(*) from ads where category_level_1_id = crwa.category_id OR category_level_2_id = crwa.category_id OR category_level_3_id = crwa.category_id)
 where crwa.region_id is NULL;");

        $this->log('Update 2');
        $this->execute("update categories_regions_with_ads as crwa
 inner join categories as c on crwa.category_id = c.id
 inner join regions as r on crwa.region_id = r.id
 set crwa.ads_count = (select count(*) from ads where (category_level_1_id = crwa.category_id) AND (region_level_1_id = crwa.region_id))
 where crwa.region_id is NOT NULL AND c.level=1 and r.level=1");

        $this->log('Update 3');
        $this->execute("update categories_regions_with_ads as crwa
 inner join categories as c on crwa.category_id = c.id
 inner join regions as r on crwa.region_id = r.id
 set crwa.ads_count = (select count(*) from ads where (category_level_2_id = crwa.category_id) AND (region_level_1_id = crwa.region_id))
 where crwa.region_id is NOT NULL  AND c.level=2 and r.level=1");

        $this->log('Update 4');
        $this->execute("update categories_regions_with_ads as crwa
 inner join categories as c on crwa.category_id = c.id
 inner join regions as r on crwa.region_id = r.id
 set crwa.ads_count = (select count(*) from ads where (category_level_3_id = crwa.category_id) AND (region_level_1_id = crwa.region_id))
 where crwa.region_id is NOT NULL AND c.level=3 and r.level=1");

        $this->log('Update 5');
        $this->execute("update categories_regions_with_ads as crwa
 inner join categories as c on crwa.category_id = c.id
 inner join regions as r on crwa.region_id = r.id
 set crwa.ads_count = (select count(*) from ads where (category_level_1_id = crwa.category_id) AND (region_level_2_id = crwa.region_id))
 where crwa.region_id is NOT NULL AND c.level=1 and r.level=2");

        $this->log('Update 6');
        $this->execute("update categories_regions_with_ads as crwa
 inner join categories as c on crwa.category_id = c.id
 inner join regions as r on crwa.region_id = r.id
 set crwa.ads_count = (select count(*) from ads where (category_level_2_id = crwa.category_id) AND (region_level_2_id = crwa.region_id))
 where crwa.region_id is NOT NULL AND c.level=2 and r.level=2");

        $this->log('Update 7');
        $this->execute("update categories_regions_with_ads as crwa
 inner join categories as c on crwa.category_id = c.id
 inner join regions as r on crwa.region_id = r.id
 set crwa.ads_count = (select count(*) from ads where (category_level_3_id = crwa.category_id) AND (region_level_2_id = crwa.region_id))
 where crwa.region_id is NOT NULL  AND c.level=3 and r.level=2");

        $this->log('Update 8');
        $this->execute("update categories_regions_with_ads as crwa
 inner join categories as c on crwa.category_id = c.id
 inner join regions as r on crwa.region_id = r.id
 set crwa.ads_count = (select count(*) from ads where (category_level_1_id = crwa.category_id) AND (region_level_3_id = crwa.region_id))
 where crwa.region_id is NOT NULL  AND c.level=1 and r.level=3");

        $this->log('Update 9');
        $this->execute("update categories_regions_with_ads as crwa
 inner join categories as c on crwa.category_id = c.id
 inner join regions as r on crwa.region_id = r.id
 set crwa.ads_count = (select count(*) from ads where (category_level_2_id = crwa.category_id) AND (region_level_3_id = crwa.region_id))
 where crwa.region_id is NOT NULL AND c.level=2 and r.level=3");

        $this->log('Update 10');
        $this->execute("update categories_regions_with_ads as crwa
 inner join categories as c on crwa.category_id = c.id
 inner join regions as r on crwa.region_id = r.id
 set crwa.ads_count = (select count(*) from ads where (category_level_3_id = crwa.category_id) AND (region_level_3_id = crwa.region_id))
 where crwa.region_id is NOT NULL AND c.level=3 and r.level=3");
    }

    private function log(string $string)
    {
        echo '[' . (new DateTime())->format('Y-m-d\TH:i:s') . '] ' . $string . PHP_EOL;
    }
}
