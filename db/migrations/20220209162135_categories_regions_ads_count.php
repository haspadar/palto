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
        $categoriesCount = $this->query('SELECT COUNT(*) AS count FROM categories')->fetchAll()[0]['count'];
        $categories = $this->query('SELECT * FROM categories')->fetchAll();

        $regionsCount = $this->query('SELECT COUNT(*) AS count FROM regions')->fetchAll()[0]['count'];
        $regions = $this->query('SELECT * FROM regions')->fetchAll();
        foreach ($categories as $categoryKey => $category) {
            foreach ($regions as $regionKey => $region) {
                echo 'Category ' . ($categoryKey + 1) . '/' .  $categoriesCount . PHP_EOL;
                echo 'Region ' . ($regionKey + 1) . '/' .  $regionsCount . PHP_EOL;
                $categoryField = "category_level_" . $category['level'] . "_id";
                $regionField = "region_level_" . $region['level'] . "_id";
                $adsCount = $this->query("SELECT COUNT(*) AS count FROM ads WHERE $categoryField = $category[id] AND $regionField = $region[id]")->fetchAll()[0]['count'];
                $this->execute("INSERT INTO categories_regions_with_ads (category_id, region_id, ads_count) VALUES($category[id], $region[id], $adsCount) ON DUPLICATE KEY UPDATE ads_count=$adsCount");
            }
        }
    }

    private function log(string $string)
    {
        echo '[' . (new DateTime())->format('Y-m-d\TH:i:s') . '] ' . $string . PHP_EOL;
    }
}
