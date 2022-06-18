<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class SettingsTable extends AbstractMigration
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
        $this->execute("CREATE TABLE `settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(100) NOT NULL DEFAULT '',
  `value` text NULL,
  `category` varchar(100) NOT NULL DEFAULT ''
);");
        $this->execute("ALTER TABLE `settings`
ADD UNIQUE `name` (`name`),
ADD INDEX `category` (`category`);");
        $this->execute("ALTER TABLE `settings`
ADD `comment` varchar(255) COLLATE 'utf8mb4_general_ci' NOT NULL DEFAULT '' AFTER `name`;
");
        $this->execute("ALTER TABLE `settings`
ADD `type` varchar(50) COLLATE 'utf8mb4_general_ci' NOT NULL DEFAULT 'text';
");
        $this->execute("ALTER TABLE `settings`
CHANGE `category` `group` varchar(50) COLLATE 'utf8mb4_general_ci' NOT NULL DEFAULT '' AFTER `value`;
");
        $this->execute("INSERT INTO `settings` (`id`, `name`, `comment`, `value`, `group`, `type`) VALUES
(1,	'template_theme',	'Тема для шаблонов',	'laspot',	'Шаблоны',	'theme');");

        $this->execute("INSERT INTO `settings` (`name`, `comment`, `value`, `group`, `type`)
VALUES ('categories_parser', 'Парсер категорий', NULL, 'Парсеры', 'categories_parser');");

        $this->execute("INSERT INTO `settings` (`name`, `comment`, `value`, `group`, `type`)
VALUES ('ads_parser', 'Парсер объявлений', '', 'Парсеры', 'ads_parser');");

        $this->execute("INSERT INTO `settings` (`name`, `comment`, `value`, `group`, `type`)
VALUES ('is_karman_panel_enabled', 'Показывать панель', NULL, 'Карман', 'bool');");

        $this->execute("CREATE TABLE `templates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `templates` (`id`, `name`) VALUES
(8,	'404.php'),
(4,	'ad.php'),
(5,	'categories-list.php'),
(2,	'hot.php'),
(1,	'index.php'),
(3,	'list.php'),
(6,	'regions-list.php'),
(7,	'registration.php');
");
        $this->execute("CREATE TABLE `pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `comment` varchar(255) NOT NULL DEFAULT '',
  `template_id` int(10) unsigned DEFAULT NULL,
  `url` varchar(255) NOT NULL DEFAULT '',
  `function` varchar(255) NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `template_id` (`template_id`),
  CONSTRAINT `pages_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `templates` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4;");
        $isHot = \Palto\Config::get('HOT_LAYOUT') == 1;
        $hotTemplateId = $this->fetchRow("SELECT id FROM templates WHERE name='" . ($isHot ? 'hot.php' : 'index.php	') . "'")['id'];
        
        $this->execute("INSERT INTO `pages` (`id`, `name`, `comment`, `template_id`, `url`, `function`, `is_enabled`) VALUES
(1, 'main', 'Главная', " . $hotTemplateId . ", '/', 'showIndex', 1),
(2, 'registration', 'Регистрация', 7, '/registration', 'showRegistration', 1),
(4, 'regions', 'Все регионы', 6, '/regions', 'showRegionsList', 1),
(5, 'categories', 'Все категории', 5, '/categories', 'showCategoriesList', 1),
(6, 'ad', 'Карточка', 4, '/([a-zA-Z0-9_-]+)/([a-zA-Z0-9_-]+)(/[a-zA-Z0-9_-]+)?(/[a-zA-Z0-9_-]+)?/ad(\\d+)', 'showAd', 1),
(8, 'region', 'Регион', 3, '/([a-zA-Z0-9_-]+)(/\\d+)?', 'showRegion', 1),
(10, 'category', 'Категория', 3, '/([a-zA-Z0-9_-]+)/([a-zA-Z0-9_-]+)(/[a-zA-Z0-9_-]+)?(/[a-zA-Z0-9_-]+)?(/\\d+)?', 'showCategory', 1),
(11, '404', '404', 8, '', 'showNotFound', 1);");


        $this->execute("UPDATE settings SET value='" . \Palto\Config::get('PARSE_ADS_SCRIPT') . "' WHERE name='ads_parser'");
        $this->execute("UPDATE settings SET value='" . \Palto\Config::get('PARSE_CATEGORIES_SCRIPT') . "' WHERE name='categories_parser'");
        $this->execute("UPDATE settings SET value='" . \Palto\Config::get('KARMAN_PANEL') . "' WHERE name='is_karman_panel_enabled'");

        $this->execute("ALTER TABLE `pages`
ADD `title` varchar(255) NOT NULL DEFAULT '',
ADD `description` text NULL AFTER `title`,
ADD `h1` varchar(255) NOT NULL DEFAULT '' AFTER `description`;");


        $this->execute("ALTER TABLE `pages`
ADD `h2` varchar(255) COLLATE 'utf8mb4_general_ci' NOT NULL DEFAULT '';");
        $this->execute("UPDATE `pages` SET
`name` = '404_ad',
`comment` = '404 для ненайденного объявления'
WHERE `name` = '404';");
        $this->execute("INSERT INTO `pages` (`name`, `comment`, `template_id`, `url`, `function`, `is_enabled`, `title`, `description`, `h1`, `h2`)
VALUES ('404_default', '404 по умолчанию', '8', '', 'showNotFound', '1', '', NULL, '', '');
");
        $this->updateHtml();
    }

    private function updateHtml()
    {
        $indexH1 = \Palto\Config::get('HOT_LAYOUT') == 1 ? \Palto\Translates::get('hot_h1') : \Palto\Translates::get('index_h1');
        $this->execute("UPDATE `pages` SET
`title` = '" . \Palto\Translates::get('index_title') . "',
`description` = '" . \Palto\Translates::get('index_description') . "',
`h1` = '" . $indexH1 . "'
WHERE `name` = 'main';");

        $this->execute("UPDATE `pages` SET
`title` = '" . \Palto\Translates::get('registration_title') . "',
`description` = '" . \Palto\Translates::get('registration_description') . "',
`h1` = '" . \Palto\Translates::get('index_h1') . "'
WHERE `name` = 'registration';");

        $this->execute("UPDATE `pages` SET
`title` = '" . \Palto\Translates::get('regions_title') . "',
`description` = '" . \Palto\Translates::get('regions_description') . "',
`h1` = '" . \Palto\Translates::get('regions_h1') . "'
WHERE `name` = 'regions';");

        $this->execute("UPDATE `pages` SET
`title` = '" . \Palto\Translates::get('categories_title') . "',
`description` = '" . \Palto\Translates::get('categories_description') . "',
`h1` = '" . \Palto\Translates::get('categories_h1') . "'
WHERE `name` = 'categories';");

        $this->execute("UPDATE `pages` SET
`title` = '" . \Palto\Translates::get('ad_title') . "',
`description` = '" . \Palto\Translates::get('categories_description') . "',
`h1` = '" . \Palto\Translates::get('ad_h1') . "'
WHERE `name` = 'ad';");

        $this->execute("UPDATE `pages` SET
`title` = '" . (\Palto\Translates::get('region_title') ?: \Palto\Translates::get('list_title')) . "',
`description` = '" . (\Palto\Translates::get('categories_description') ?: \Palto\Translates::get('list_description')) . "',
`h1` = '" . \Palto\Translates::get('list_h1') . "'
WHERE `name` = 'region';");

        $this->execute("UPDATE `pages` SET
`title` = '" . (\Palto\Translates::get('list_title')) . "',
`description` = '" . (\Palto\Translates::get('list_description')) . "',
`h1` = '" . \Palto\Translates::get('list_h1') . "'
WHERE `name` = 'category';");

        $this->execute("UPDATE `pages` SET
`title` = '404',
`description` = '404',
`h1` = '" . \Palto\Translates::get('404_h1_ad') . "',
`h2` = '" . \Palto\Translates::get('404_h2') . "'
WHERE `name` = '404_ad';");

        $this->execute("UPDATE `pages` SET
`title` = '404',
`description` = '404',
`h1` = '" . \Palto\Translates::get('404_h1_list') . "',
`h2` = '" . \Palto\Translates::get('404_h2') . "'
WHERE `name` = '404_default';");
    }
}
