<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class RegionsCategoriesDuplicatesRemove extends AbstractMigration
{
    public function change(): void
    {
        $minRegionRow = $this->fetchRow("SELECT MIN(create_time) as create_time from regions where url like '%-1' or url like '%-2' or url like '%-3';");
        $minCategoryRow = $this->fetchRow("SELECT MIN(create_time) as create_time from categories where url like '%-1' or url like '%-2' or url like '%-3';");
        if ($minRegionRow && $minCategoryRow) {
            $minDate = min(new DateTime($minRegionRow['create_time']), new DateTime($minCategoryRow['create_time']));
        } elseif ($minRegionRow) {
            $minDate = new DateTime($minRegionRow['create_time']);
        } elseif ($minCategoryRow) {
            $minDate = new DateTime($minCategoryRow['create_time']);
        }

        if (isset($minDate)) {
            echo 'Found min create_time=' . $minDate->format('Y-m-d H:i:s') . PHP_EOL;
            $this->execute("DELETE FROM regions WHERE create_time >= '" . $minDate->format('Y-m-d H:i:s') . "'");
            $this->execute("DELETE FROM categories WHERE create_time >= '" . $minDate->format('Y-m-d H:i:s') . "'");
            $this->execute("DELETE FROM ads WHERE create_time >= '" . $minDate->format('Y-m-d H:i:s') . "'");
        }
    }
}
