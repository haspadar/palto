<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CategorySynonymAndField extends AbstractMigration
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
        $this->execute("ALTER TABLE `ads`
ADD `synonym_id` int(10) unsigned NULL AFTER `deleted_time`,
ADD `field` enum('title','text') COLLATE 'utf8mb4_general_ci' NULL AFTER `synonym_id`,
ADD FOREIGN KEY (`synonym_id`) REFERENCES `synonyms` (`id`) ON DELETE SET NULL,
ADD INDEX `field` (`field`);");
        \Palto\Logger::info('Refind and move started');

        \Palto\Synonyms::findAndMoveAds(\Palto\Categories::getLiveCategories());
    }
}
