<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Synonyms extends AbstractMigration
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
        $this->execute("CREATE TABLE `synonyms` (
  `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `category_id` int(10) unsigned NULL,
  `title` varchar(255) COLLATE 'utf8mb4_general_ci' NOT NULL DEFAULT '',
  FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
);");
        $this->execute("ALTER TABLE `synonyms`
ADD UNIQUE `title_category_id` (`title`, `category_id`);");
    }
}
