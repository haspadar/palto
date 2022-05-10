<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class SynonymsEqualCategoryTitle extends AbstractMigration
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
        $leafs = \Palto\Categories::getLeafs();
        foreach ($leafs as $category) {
            $titles = array_map(fn(\Palto\Synonym $synonym) => mb_strtolower($synonym->getTitle()), $category->getSynonyms());
            if (!in_array(mb_strtolower($category->getTitle()), $titles)) {
                \Palto\Model\Synonyms::add(mb_strtolower($category->getTitle()), $category->getId());
            }
        }
    }
}
