<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AdsCounts extends AbstractMigration
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
        $this->execute("ALTER TABLE `categories` ADD COLUMN `ads_count` INT(10)  UNSIGNED NOT NULL DEFAULT '0';");
        $this->execute("ALTER TABLE `categories` CHANGE `right_id` `right_id` int(10) unsigned NOT NULL DEFAULT '0';;");
        $this->execute("ALTER TABLE `categories` CHANGE `left_id` `left_id` int(10) unsigned NOT NULL DEFAULT '0';;");

        $this->execute("ALTER TABLE `regions` ADD COLUMN `ads_count` INT(10)  UNSIGNED NOT NULL DEFAULT '0';");
        $this->execute("ALTER TABLE `regions` CHANGE `right_id` `right_id` int(10) unsigned NOT NULL DEFAULT '0';;");
        $this->execute("ALTER TABLE `regions` CHANGE `left_id` `left_id` int(10) unsigned NOT NULL DEFAULT '0';;");

        \Palto\Categories::rebuildAdsCount();
        \Palto\Regions::rebuildAdsCount();
    }
}
