<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class NestedSets extends AbstractMigration
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
        $this->execute("/* 17:14:33 MacOS uspets */ ALTER TABLE `categories` ADD `left_id` INT(10)  UNSIGNED  NULL  AFTER `create_time`;");
        $this->execute("/* 17:14:43 MacOS uspets */ ALTER TABLE `categories` ADD `right_id` INT(10)  UNSIGNED  NULL  DEFAULT NULL  AFTER `left_id`;");
        \Palto\Categories::rebuildTree();

        $this->execute("/* 17:14:33 MacOS uspets */ ALTER TABLE `regions` ADD `left_id` INT(10)  UNSIGNED  NULL  AFTER `create_time`;");
        $this->execute("/* 17:14:43 MacOS uspets */ ALTER TABLE `regions` ADD `right_id` INT(10)  UNSIGNED  NULL  DEFAULT NULL  AFTER `left_id`;");
        \Palto\Regions::rebuildTree();

        $this->execute("ALTER TABLE `categories_regions_with_ads` DROP `ads_count`, RENAME TO `live`;");
        $this->execute("ALTER TABLE `live` ADD `create_time` timestamp NULL;");
        $this->execute("ALTER TABLE `live` ADD INDEX `create_time` (`create_time`);");

    }
}
