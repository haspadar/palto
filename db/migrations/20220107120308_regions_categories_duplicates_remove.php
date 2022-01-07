<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class RegionsCategoriesDuplicatesRemove extends AbstractMigration
{
    public function change(): void
    {
        $regionsOriginals = $this->fetchAll("select * from regions where url not REGEXP '-[[:digit:]]$'");
        $regionsDuplicates = $this->fetchAll("select * from regions where url REGEXP '-[[:digit:]]$'");
        echo 'Found ' . count($regionsDuplicates) . ' $regionsDuplicates' . PHP_EOL;
        $regionIds = $this->replaceDuplicates($regionsDuplicates, $regionsOriginals, 'region');
        if ($regionIds) {
            echo 'Try remove empty regions' . PHP_EOL;
            $this->execute('DELETE from regions where id IN(' . implode(',', $regionIds) . ')');
        }

        $categoriesOriginals = $this->fetchAll("select * from categories where url not REGEXP '-[[:digit:]]$'");
        $categoriesDuplicates = $this->fetchAll("select * from categories where url REGEXP '-[[:digit:]]$'");
        echo 'Found ' . count($categoriesDuplicates) . ' $categoriesDuplicates' . PHP_EOL;
        $categoryIds = $this->replaceDuplicates($categoriesDuplicates, $categoriesOriginals, 'category');
        if ($categoryIds) {
            echo 'Try remove empty categories' . PHP_EOL;
            $this->execute('DELETE from categories where id IN(' . implode(',', $categoryIds) . ')');
        }
    }

    private function replaceDuplicates(array $duplicates, array $originals, string $singleName): array
    {
        $groupedById = $this->groupBy($originals, 'id');
        $groupedByUrl = $this->groupBy($originals, 'url');
        $removableIds = [];
        foreach ($duplicates as $duplicate) {
            $parts = explode('-', $duplicate['url']);
            unset($parts[count($parts) - 1]);
            $url = implode('-', $parts);
            $original = $groupedByUrl[$url] ?? [];
            if ($original) {
                list($level1Id, $level2Id, $level3Id) = $this->getLevels($original, $groupedById);
                $this->execute("UPDATE ads SET {$singleName}_id=" . $original['id'] . ",{$singleName}_level_1_id=$level1Id,{$singleName}_level_2_id=$level2Id,{$singleName}_level_3_id=$level3Id  WHERE {$singleName}_id=" . $duplicate['id']);
                $removableIds[] = $duplicate['id'];
            }
        }

        return $removableIds;
    }

    private function groupBy(array $data, string $groupField)
    {
        $grouped = [];
        foreach ($data as $value) {
            $grouped[$value[$groupField]] = $value;
        }

        return $grouped;
    }

    private function getLevels(mixed $original, array $groupedById): array
    {
        $level1Id = $level2Id = $level3Id = 'null';
        $level = $original['level'];
        while ($level >= 1 && $original) {
            ${'level' . $level . 'Id'} = $original['id'];
            $level--;
            $original = $original['parent_id'] ? $groupedById[$original['parent_id']] : [];
        }

        return [$level1Id, $level2Id, $level3Id];
    }
}
