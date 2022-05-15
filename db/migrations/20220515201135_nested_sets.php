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
        $this->execute("/* 17:17:01 MacOS uspets */ DELETE FROM `categories`WHERE url='';");
        $this->execute("/* 17:17:01 MacOS uspets */ INSERT INTO `categories` (`title`, `level`, `parent_id`) VALUES ('Категории', 0, null);");
        $this->execute("/* 17:17:01 MacOS uspets */ UPDATE `categories` SET parent_id=" . $this->getAdapter()->getConnection()->lastInsertId() . ' WHERE parent_id IS NULL  AND level=1');
        \Palto\Categories::rebuildTree();

        $this->execute("/* 17:14:33 MacOS uspets */ ALTER TABLE `regions` ADD `left_id` INT(10)  UNSIGNED  NULL  AFTER `create_time`;");
        $this->execute("/* 17:14:43 MacOS uspets */ ALTER TABLE `regions` ADD `right_id` INT(10)  UNSIGNED  NULL  DEFAULT NULL  AFTER `left_id`;");
        $this->execute("/* 17:17:01 MacOS uspets */ DELETE FROM `regions`WHERE url='';");
        $this->execute("/* 17:17:01 MacOS uspets */ INSERT INTO `regions` (`title`, `level`, `parent_id`) VALUES ('Регионы', 0, null);");
        $this->execute("/* 17:17:01 MacOS uspets */ UPDATE `regions` SET parent_id=" . $this->getAdapter()->getConnection()->lastInsertId() . ' WHERE parent_id IS NULL AND level=1');
        \Palto\Regions::rebuildTree();

    }
}
