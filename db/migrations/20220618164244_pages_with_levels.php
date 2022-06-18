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
`name` = 'region_0_category_1',
`comment` = 'Регион 0-го уровня, категория 1-го уровня'
WHERE `name` = 'region';");
        $this->execute("INSERT INTO `pages` (`name`, `comment`, `template_id`, `url`, `function`, `is_enabled`, `title`, `description`, `h1`, `h2`)
SELECT 'region_0_category_2', 'Регион 0-го уровня, категория 2-го уровня',`template_id`, `url`, `function`, `is_enabled`, `title`, `description`, `h1`, `h2`
FROM `pages`
WHERE ((`name` = 'region_0_category_1'));");
        $this->execute("INSERT INTO `pages` (`name`, `comment`, `template_id`, `url`, `function`, `is_enabled`, `title`, `description`, `h1`, `h2`)
SELECT 'region_1_category_1', 'Регион 1-го уровня, категория 1-го уровня', `template_id`, `url`, `function`, `is_enabled`, `title`, `description`, `h1`, `h2`
FROM `pages`
WHERE ((`name` = 'region_0_category_2'));");
        $this->execute("INSERT INTO `pages` (`name`, `comment`, `template_id`, `url`, `function`, `is_enabled`, `title`, `description`, `h1`, `h2`)
SELECT 'region_1_category_2', 'Регион 1-го уровня, категория 2-го уровня', `template_id`, `url`, `function`, `is_enabled`, `title`, `description`, `h1`, `h2`
FROM `pages`
WHERE ((`name` = 'region_1_category_1'));");


        $this->execute("INSERT INTO `pages` (`name`, `comment`, `template_id`, `url`, `function`, `is_enabled`, `title`, `description`, `h1`, `h2`)
SELECT 'region_2_category_1', 'Регион 2-го уровня, категория 1-го уровня', `template_id`, `url`, `function`, `is_enabled`, `title`, `description`, `h1`, `h2`
FROM `pages`
WHERE ((`name` = 'region_1_category_2'));");
        $this->execute("INSERT INTO `pages` (`name`, `comment`, `template_id`, `url`, `function`, `is_enabled`, `title`, `description`, `h1`, `h2`)
SELECT 'region_2_category_2', 'Регион 2-го уровня, категория 2-го уровня', `template_id`, `url`, `function`, `is_enabled`, `title`, `description`, `h1`, `h2`
FROM `pages`
WHERE ((`name` = 'region_2_category_1'));");

        $this->execute("INSERT INTO `pages` (`name`, `comment`, `template_id`, `url`, `function`, `is_enabled`, `title`, `description`, `h1`, `h2`)
SELECT 'region_3_category_1', 'Регион 3-го уровня, категория 1-го уровня', `template_id`, `url`, `function`, `is_enabled`, `title`, `description`, `h1`, `h2`
FROM `pages`
WHERE ((`name` = 'region_2_category_2'));");
        $this->execute("INSERT INTO `pages` (`name`, `comment`, `template_id`, `url`, `function`, `is_enabled`, `title`, `description`, `h1`, `h2`)
SELECT 'region_3_category_2', 'Регион 3-го уровня, категория 2-го уровня', `template_id`, `url`, `function`, `is_enabled`, `title`, `description`, `h1`, `h2`
FROM `pages`
WHERE ((`name` = 'region_3_category_1'));");
    }
}
