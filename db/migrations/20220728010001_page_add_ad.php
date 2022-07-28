<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class PageAddAd extends AbstractMigration
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
        $this->execute("INSERT INTO `uspets`.`templates` (`id`, `name`) VALUES (8, 'add.php');");
        $this->execute("INSERT INTO `pages` (`name`, `comment`, `url`, `function`, `is_enabled`, `priority`, `router_priority`,`template_id`) VALUES ('add', 'Добавление объявления', '/add', 'showAdd', 0, 31, 31, 9);");

    }
}
