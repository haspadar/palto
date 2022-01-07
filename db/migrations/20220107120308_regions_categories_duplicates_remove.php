<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class RegionsCategoriesDuplicatesRemove extends AbstractMigration
{
    public function change(): void
    {
        $regionsDuplicates = $this->fetchAll("select * from regions where url REGEXP '-[[:digit:]]$'");
        echo 'Found ' . count($regionsDuplicates) . ' $regionsDuplicates' . PHP_EOL;
        foreach ($regionsDuplicates as $key => $regionsDuplicate) {
            echo ($key + 1) . '/' . count($regionsDuplicates) . ' regions' . PHP_EOL;
            $parts = explode('-', $regionsDuplicate['url']);
            unset($parts[count($parts) - 1]);
            $url = implode('-', $parts);
            $original = $this->fetchRow('select * from regions where url="' . $url . '"');
            echo 'Original for "' . $regionsDuplicate['url'] . '" is "' . $url . '" (id=' . $original['id'] , ')' . PHP_EOL;
            if ($original) {
                $this->execute("UPDATE ads SET region_id=" . $original['id'] . " WHERE region_id=" . $regionsDuplicate['id']);
            }

        }

        $categoriesDuplicates = $this->fetchAll("select * from categories where url REGEXP '-[[:digit:]]$'");
        echo 'Found ' . count($categoriesDuplicates) . ' $categoriesDuplicates' . PHP_EOL;
        foreach ($categoriesDuplicates as $key => $categoriesDuplicate) {
            echo ($key + 1) . '/' . count($categoriesDuplicates) . ' categories' . PHP_EOL;
            $parts = explode('-', $categoriesDuplicate['url']);
            unset($parts[count($parts) - 1]);
            $url = implode('-', $parts);
            $original = $this->fetchRow('select * from categories where url="' . $url . '"');
            echo 'Original for "' . $categoriesDuplicate['url'] . '" is "' . $url . '" (id=' . $original['id'] , ')' . PHP_EOL;
            if ($original) {
                $this->execute("UPDATE ads SET category_id=" . $original['id'] . " WHERE category_id=" . $categoriesDuplicate['id']);
            }

        }

        echo 'Update 1' . PHP_EOL;
        $this->execute('UPDATE ads AS a INNER JOIN regions AS r ON a.region_id=r.id LEFT JOIN regions AS r2 ON r.parent_id = r2.id SET a.region_level_3_id=a.region_id, a.region_level_2_id=r.parent_id, a.region_level_1_id=r2.parent_id WHERE r.level=3');
        echo 'Update 2' . PHP_EOL;
        $this->execute('UPDATE ads AS a INNER JOIN regions AS r ON a.region_id=r.id SET a.region_level_3_id=NULL, a.region_level_2_id=a.region_id, a.region_level_1_id=r.parent_id WHERE r.level=2');
        echo 'Update 3' . PHP_EOL;
        $this->execute('UPDATE ads AS a INNER JOIN regions AS r ON a.region_id=r.id SET a.region_level_3_id=NULL, a.region_level_2_id=NULL, a.region_level_1_id=a.region_id WHERE r.level=1');
        echo 'Update 4' . PHP_EOL;
        $this->execute('UPDATE ads AS a INNER JOIN regions AS r ON a.region_id=r.id SET a.region_level_2_id=a.region_id, a.region_level_1_id = r.parent_id WHERE r.level=2');
        echo 'Update 5' . PHP_EOL;
        $this->execute('UPDATE ads AS a INNER JOIN regions AS r ON a.region_id=r.id SET a.region_level_2_id=NULL, a.region_level_1_id = a.region_id WHERE r.level=1');
        echo 'Update 6' . PHP_EOL;
        $this->execute('UPDATE ads AS a INNER JOIN categories AS c ON a.category_id=c.id LEFT JOIN categories AS c2 ON c.parent_id = c2.id SET a.category_level_3_id=a.category_id, a.category_level_2_id=c.parent_id, a.category_level_1_id=c2.parent_id WHERE c.level=3');
        echo 'Update 7' . PHP_EOL;
        $this->execute('UPDATE ads AS a INNER JOIN categories AS c ON a.category_id=c.id SET a.category_level_3_id=NULL, a.category_level_2_id=a.category_id, a.category_level_1_id=c.parent_id WHERE c.level=2');
        echo 'Update 8' . PHP_EOL;
        $this->execute('UPDATE ads AS a INNER JOIN categories AS c ON a.category_id=c.id SET a.category_level_3_id=NULL, a.category_level_2_id=NULL, a.category_level_1_id=a.category_id WHERE c.level=1');
    }
}
