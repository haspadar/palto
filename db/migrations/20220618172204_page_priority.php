<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class PagePriority extends AbstractMigration
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
        $this->execute("ALTER TABLE `pages`
ADD `priority` int unsigned NOT NULL DEFAULT '0';");
        $this->execute("ALTER TABLE `pages`
ADD INDEX `priority` (`priority`);");

        $this->execute("UPDATE `pages` SET`priority` = '10' WHERE `name` = 'main';");
        $this->execute("UPDATE `pages` SET`priority` = '20' WHERE `name` = 'registration';");


        $this->execute("UPDATE `pages` SET`priority` = '30' WHERE `name` = 'categories';");
        $this->execute("UPDATE `pages` SET`priority` = '40' WHERE `name` = 'regions';");

        $this->execute("UPDATE `pages` SET`priority` = '50' WHERE `name` = 'region_0';");
        $this->execute("UPDATE `pages` SET`priority` = '60' WHERE `name` = 'region_1';");
        $this->execute("UPDATE `pages` SET`priority` = '70' WHERE `name` = 'region_2';");
        $this->execute("UPDATE `pages` SET`priority` = '80' WHERE `name` = 'region_3';");


        $this->execute("UPDATE `pages` SET`priority` = '90' WHERE `name` = 'region_0_category_1';");
        $this->execute("UPDATE `pages` SET`priority` = '100' WHERE `name` = 'region_0_category_2';");

        $this->execute("UPDATE `pages` SET`priority` = '110' WHERE `name` = 'region_1_category_1';");
        $this->execute("UPDATE `pages` SET`priority` = '120' WHERE `name` = 'region_1_category_2';");

        $this->execute("UPDATE `pages` SET`priority` = '130' WHERE `name` = 'region_2_category_1';");
        $this->execute("UPDATE `pages` SET`priority` = '140' WHERE `name` = 'region_2_category_2';");

        $this->execute("UPDATE `pages` SET`priority` = '150' WHERE `name` = 'region_3_category_1';");
        $this->execute("UPDATE `pages` SET`priority` = '160' WHERE `name` = 'region_3_category_2';");

        $this->execute("UPDATE `pages` SET`priority` = '170' WHERE `name` = 'ad';");
        $this->execute("UPDATE `pages` SET`priority` = '180' WHERE `name` = '404_ad';");
        $this->execute("UPDATE `pages` SET`priority` = '190' WHERE `name` = '404_default';");
    }
}
