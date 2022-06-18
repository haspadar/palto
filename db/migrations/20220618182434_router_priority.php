<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class RouterPriority extends AbstractMigration
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
ADD `router_priority` int(10) unsigned NOT NULL DEFAULT '0';");
        $this->execute("ALTER TABLE `pages`
ADD INDEX `router_priority` (`router_priority`);");
        $this->execute("UPDATE `pages` SET `router_priority` = '10' WHERE `name` = 'main';");
        $this->execute("UPDATE `pages` SET `router_priority` = '20' WHERE `name` = 'registration';");
        $this->execute("UPDATE `pages` SET `router_priority` = '30' WHERE `name` = 'categories';");
        $this->execute("UPDATE `pages` SET `router_priority` = '40' WHERE `name` = 'regions';");
        $this->execute("UPDATE `pages` SET `router_priority` = '50' WHERE `name` = 'ad';");
        $this->execute("UPDATE `pages` SET `function` = 'showRegion', url='/([a-zA-Z0-9_-]+)(/\d+)?',router_priority=60 WHERE `name` in('region_0','region_1','region_2','region_3');");
        $this->execute("UPDATE `pages` SET router_priority = 70 WHERE `function`='showCategory';");
        $this->execute("UPDATE `pages` SET
`url` = '/([a-zA-Z0-9_-]+)/([a-zA-Z0-9_-]+)(/[a-zA-Z0-9_-]+)?(/[a-zA-Z0-9_-]+)?/ad(\\d+)'
WHERE `name` = 'ad';");
    }
}
