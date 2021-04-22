<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CategoriesRegionsAds extends AbstractMigration
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
        $this->execute(
            "

CREATE TABLE `regions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL DEFAULT '',
  `parent_id` int(10) unsigned DEFAULT NULL,
  `url` varchar(200) NOT NULL DEFAULT '',
  `level` int(10) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `url` (`url`),
  KEY `title` (`title`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `regions_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `regions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned DEFAULT NULL,
  `level` int(11) unsigned NOT NULL DEFAULT 1,
  `title` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `donor_url` varchar(500) NOT NULL DEFAULT '',
  `icon_url` varchar(1024) NOT NULL DEFAULT '',
  `icon_text` text DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `donor_url` (`donor_url`),
  KEY `title` (`title`),
  KEY `parent_id` (`parent_id`),
  KEY `url` (`url`),
  KEY `level` (`level`),
  CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `ads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(600) NOT NULL DEFAULT '',
  `category_id` int(11) unsigned DEFAULT NULL,
  `region_id` int(10) unsigned DEFAULT NULL,
  `title` varchar(1000) NOT NULL DEFAULT '',
  `text` text DEFAULT NULL,
  `address` varchar(1000) NOT NULL DEFAULT '',
  `coordinates` varchar(1000) NOT NULL DEFAULT '',
  `post_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`),
  KEY `category_id` (`category_id`),
  KEY `region_id` (`region_id`),
  CONSTRAINT `ads_ibfk_3` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `ads_ibfk_4` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `ads_images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ad_id` int(11) DEFAULT NULL,
  `big` varchar(1000) NOT NULL DEFAULT '',
  `small` varchar(1000) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `ad_id` (`ad_id`),
  CONSTRAINT `ads_images_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
"
        );
    }
}
