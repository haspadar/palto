<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AuthTextReplace extends AbstractMigration
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
        $this->execute("UPDATE settings SET comment='Пароль для авторизации' WHERE comment='Пароль для аутентификации'");
        $this->execute("UPDATE settings SET comment='Логин для авторизации' WHERE comment='Логин для аутентификации'");
    }
}
