<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class PagesWithLevels extends AbstractMigration
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
        $this->execute("UPDATE `pages` SET
`name` = 'region_1',
`comment` = 'Регион 1-го уровня'
WHERE `name` = 'region';");

        $this->execute("INSERT INTO `pages` (`name`, `comment`, `template_id`, `url`, `function`, `is_enabled`, `title`, `description`, `h1`, `h2`)
SELECT 'region_2', 'Регион 2-го уровня', `template_id`, `url`, `function`, `is_enabled`, `title`, `description`, `h1`, `h2`
FROM `pages`
WHERE ((`name` = 'region_1'));");

        $this->execute("INSERT INTO `pages` (`name`, `comment`, `template_id`, `url`, `function`, `is_enabled`, `title`, `description`, `h1`, `h2`)
SELECT 'region_3', 'Регион 3-го уровня', `template_id`, `url`, `function`, `is_enabled`, `title`, `description`, `h1`, `h2`
FROM `pages`
WHERE ((`name` = 'region_1'));");

        $this->execute("INSERT INTO `pages` (`name`, `comment`, `template_id`, `url`, `function`, `is_enabled`, `title`, `description`, `h1`, `h2`)
SELECT 'region_4', 'Регион 4-го уровня', `template_id`, `url`, `function`, `is_enabled`, `title`, `description`, `h1`, `h2`
FROM `pages`
WHERE ((`name` = 'region_1'));");

        $this->execute("UPDATE `pages` SET
`name` = 'category_1',
`comment` = 'Категория 1-го уровня'
WHERE `name` = 'category';");

        $this->execute("INSERT INTO `pages` (`name`, `comment`, `template_id`, `url`, `function`, `is_enabled`, `title`, `description`, `h1`, `h2`)
SELECT 'category_2', 'Категория 2-го уровня', `template_id`, `url`, `function`, `is_enabled`, `title`, `description`, `h1`, `h2`
FROM `pages`
WHERE ((`name` = 'category_1'));");
    }
}
