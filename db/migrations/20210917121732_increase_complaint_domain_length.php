<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class IncreaseComplaintDomainLength extends AbstractMigration
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
        $this->execute("ALTER TABLE `complaints` CHANGE `domain` `domain` VARCHAR(100)  CHARACTER SET utf8mb4  COLLATE utf8mb4_general_ci  NOT NULL  DEFAULT '';");
    }
}
