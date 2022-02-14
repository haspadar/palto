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
          `category_level` int(11) unsigned NOT NULL DEFAULT '0',
          `child_id` int(11) unsigned NULL,
          `child_level` int(11) unsigned NOT NULL DEFAULT '0',
          FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
          FOREIGN KEY (`child_id`) REFERENCES `categories` (`id`)
        );");
        $this->addTableLinks('categories','category_links', 'category_id');
        $this->execute("DROP TABLE IF EXISTS region_links");
        $this->execute("CREATE TABLE `region_links` (
          `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `region_id` int(11) unsigned NULL,
          `region_level` int(11) unsigned NOT NULL DEFAULT '0',
          `child_id` int(11) unsigned NULL,
          `child_level` int(11) unsigned NOT NULL DEFAULT '0',
          FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`),
          FOREIGN KEY (`child_id`) REFERENCES `regions` (`id`)
        );");
        $this->addTableLinks('regions','region_links', 'region_id');
    }

    private function getParentIds(int $parentId, string $table): array
    {
        $parentIds = [];
        while ($parent = $this->fetchAll("SELECT * FROM $table WHERE id = $parentId")) {
            $parentIds[] = $parent[0]['id'];
            $parentId = intval($parent[0]['parent_id']);
        }

        return $parentIds;
    }

    private function addLinks(int $id, array $parentIds, string $linksTable, string $linkField)
    {
        foreach ($parentIds as $parentId) {
            $this->table($linksTable, [
                $linkField => $parentId,
                'child_id' => $id
            ]);
        }
    }

    private function addTableLinks(string $table, string $linksTable, string $linkField)
    {
        $maxLevel = $this->fetchAll("SELECT MAX(level) as max_level FROM $table")[0]['max_level'];
        for ($level = $maxLevel; $level >= 1; $level--) {
            echo 'Level ' . $level . PHP_EOL;
            $levelRows = $this->fetchAll("SELECT id, parent_id FROM $table WHERE level = $level");
            echo 'Found ' . count($levelRows) . " $table" . PHP_EOL;
            foreach ($levelRows as $key => $row) {
                $parentIds = $this->getParentIds(intval($row['parent_id']), $table);
                $this->addLinks(intval($row['id']), $parentIds, $linksTable, $linkField);
                echo "Added links for $linkField " . ($key + 1) . '/' . count($levelRows) . ' (level ' . $level . ')' .PHP_EOL;
            }
        }
    }
}
