-- Adminer 4.8.1 MySQL 10.3.34-MariaDB-0ubuntu0.20.04.1 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
                              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                              `parent_id` int(11) unsigned DEFAULT NULL,
                              `level` int(11) unsigned NOT NULL DEFAULT 1,
                              `tree_id` int(10) unsigned DEFAULT 1,
                              `title` varchar(255) NOT NULL DEFAULT '',
                              `url` varchar(255) NOT NULL DEFAULT '',
                              `donor_url` varchar(500) NOT NULL DEFAULT '',
                              `icon_url` varchar(1024) NOT NULL DEFAULT '',
                              `icon_text` text DEFAULT NULL,
                              `emoji` text DEFAULT NULL,
                              `create_time` timestamp NULL DEFAULT NULL,
                              PRIMARY KEY (`id`),
                              UNIQUE KEY `url_level` (`url`,`level`),
                              KEY `title` (`title`),
                              KEY `parent_id` (`parent_id`),
                              KEY `url` (`url`),
                              KEY `level` (`level`),
                              KEY `tree_id` (`tree_id`),
                              CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `regions`;
CREATE TABLE `regions` (
                           `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                           `title` varchar(200) NOT NULL DEFAULT '',
                           `parent_id` int(11) unsigned DEFAULT NULL,
                           `url` varchar(200) NOT NULL DEFAULT '',
                           `donor_url` varchar(500) NOT NULL DEFAULT '',
                           `icon_url` varchar(1024) NOT NULL DEFAULT '',
                           `icon_text` text DEFAULT NULL,
                           `create_time` timestamp NULL DEFAULT NULL,
                           `level` int(10) unsigned NOT NULL DEFAULT 1,
                           `tree_id` int(10) unsigned DEFAULT 1,
                           PRIMARY KEY (`id`),
                           UNIQUE KEY `url` (`url`),
                           KEY `title` (`title`),
                           KEY `parent_id` (`parent_id`),
                           KEY `tree_id` (`tree_id`),
                           CONSTRAINT `regions_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `ads`;
CREATE TABLE `ads` (
                       `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                       `url` varchar(600) NOT NULL DEFAULT '',
                       `category_id` int(11) unsigned DEFAULT NULL,
                       `category_level_3_id` int(11) unsigned DEFAULT NULL,
                       `category_level_2_id` int(11) unsigned DEFAULT NULL,
                       `category_level_1_id` int(11) unsigned DEFAULT NULL,
                       `region_id` int(11) unsigned DEFAULT NULL,
                       `region_level_2_id` int(11) unsigned DEFAULT NULL,
                       `region_level_3_id` int(11) unsigned DEFAULT NULL,
                       `region_level_1_id` int(11) unsigned DEFAULT NULL,
                       `title` varchar(1000) NOT NULL DEFAULT '',
                       `text` text DEFAULT NULL,
                       `address` varchar(1000) NOT NULL DEFAULT '',
                       `coordinates` varchar(1000) NOT NULL DEFAULT '',
                       `post_time` timestamp NULL DEFAULT NULL,
                       `price` decimal(10,2) unsigned NOT NULL DEFAULT 0.00,
                       `currency` varchar(20) NOT NULL DEFAULT '',
                       `seller_name` varchar(100) NOT NULL DEFAULT '',
                       `seller_postfix` varchar(100) NOT NULL DEFAULT '',
                       `seller_phone` varchar(100) NOT NULL DEFAULT '',
                       `deleted_time` timestamp NULL DEFAULT NULL,
                       `create_time` timestamp NULL DEFAULT NULL,
                       PRIMARY KEY (`id`),
                       UNIQUE KEY `url` (`url`),
                       KEY `category_id` (`category_id`),
                       KEY `region_id` (`region_id`),
                       KEY `create_time` (`create_time`),
                       KEY `post_time` (`post_time`),
                       KEY `deleted_time` (`deleted_time`),
                       KEY `category_level_3_id` (`category_level_3_id`),
                       KEY `category_level_2_id` (`category_level_2_id`),
                       KEY `category_level_1_id` (`category_level_1_id`),
                       KEY `region_level_2_id` (`region_level_2_id`),
                       KEY `region_level_1_id` (`region_level_1_id`),
                       KEY `region_level_3_id` (`region_level_3_id`),
                       CONSTRAINT `ads_ibfk_10` FOREIGN KEY (`region_level_3_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE,
                       CONSTRAINT `ads_ibfk_3` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
                       CONSTRAINT `ads_ibfk_4` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE,
                       CONSTRAINT `ads_ibfk_5` FOREIGN KEY (`category_level_3_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
                       CONSTRAINT `ads_ibfk_6` FOREIGN KEY (`category_level_2_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
                       CONSTRAINT `ads_ibfk_7` FOREIGN KEY (`category_level_1_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
                       CONSTRAINT `ads_ibfk_8` FOREIGN KEY (`region_level_2_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE,
                       CONSTRAINT `ads_ibfk_9` FOREIGN KEY (`region_level_1_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `ads_details`;
CREATE TABLE `ads_details` (
                               `id` int(11) NOT NULL AUTO_INCREMENT,
                               `details_field_id` int(11) unsigned DEFAULT NULL,
                               `ad_id` int(11) unsigned DEFAULT NULL,
                               `value` text NOT NULL DEFAULT '',
                               PRIMARY KEY (`id`),
                               KEY `details_field_id` (`details_field_id`),
                               KEY `ad_id` (`ad_id`),
                               CONSTRAINT `ads_details_ibfk_1` FOREIGN KEY (`details_field_id`) REFERENCES `details_fields` (`id`) ON DELETE CASCADE,
                               CONSTRAINT `ads_details_ibfk_2` FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `ads_images`;
CREATE TABLE `ads_images` (
                              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                              `ad_id` int(11) unsigned DEFAULT NULL,
                              `big` varchar(1000) NOT NULL DEFAULT '',
                              `small` varchar(1000) NOT NULL DEFAULT '',
                              PRIMARY KEY (`id`),
                              KEY `ad_id` (`ad_id`),
                              CONSTRAINT `ads_images_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `categories_regions_with_ads`;
CREATE TABLE `categories_regions_with_ads` (
                                               `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                                               `category_id` int(11) unsigned DEFAULT NULL,
                                               `region_id` int(11) unsigned DEFAULT NULL,
                                               `ads_count` int(10) unsigned NOT NULL DEFAULT 0,
                                               PRIMARY KEY (`id`),
                                               UNIQUE KEY `category_id_region_id` (`category_id`,`region_id`),
                                               KEY `region_id` (`region_id`),
                                               CONSTRAINT `categories_regions_with_ads_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
                                               CONSTRAINT `categories_regions_with_ads_ibfk_2` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `complaints`;
CREATE TABLE `complaints` (
                              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                              `email` varchar(200) NOT NULL DEFAULT '',
                              `message` text DEFAULT NULL,
                              `ad_id` int(11) unsigned DEFAULT NULL,
                              `response_time` timestamp NULL DEFAULT NULL,
                              `ignore_time` timestamp NULL DEFAULT NULL,
                              `ip` varchar(20) NOT NULL DEFAULT '',
                              `domain` varchar(100) NOT NULL DEFAULT '',
                              `page` varchar(100) NOT NULL DEFAULT '',
                              `create_time` timestamp NULL DEFAULT NULL,
                              PRIMARY KEY (`id`),
                              KEY `create_time` (`create_time`),
                              KEY `ip` (`ip`),
                              KEY `domain` (`domain`),
                              KEY `page` (`page`),
                              KEY `ad_id` (`ad_id`),
                              KEY `response_time_ignore_time` (`response_time`,`ignore_time`),
                              CONSTRAINT `complaints_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `details_fields`;
CREATE TABLE `details_fields` (
                                  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                                  `category_id` int(11) unsigned DEFAULT NULL,
                                  `field` varchar(100) DEFAULT NULL,
                                  PRIMARY KEY (`id`),
                                  UNIQUE KEY `category_field` (`category_id`,`field`),
                                  CONSTRAINT `details_fields_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `phinxlog`;
CREATE TABLE `phinxlog` (
                            `version` bigint(20) NOT NULL,
                            `migration_name` varchar(100) DEFAULT NULL,
                            `start_time` timestamp NULL DEFAULT NULL,
                            `end_time` timestamp NULL DEFAULT NULL,
                            `breakpoint` tinyint(1) NOT NULL DEFAULT 0,
                            PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `synonyms`;
CREATE TABLE `synonyms` (
                            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                            `category_id` int(10) unsigned DEFAULT NULL,
                            `title` varchar(255) NOT NULL DEFAULT '',
                            PRIMARY KEY (`id`),
                            UNIQUE KEY `title_category_id` (`title`,`category_id`),
                            KEY `category_id` (`category_id`),
                            CONSTRAINT `synonyms_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `translates`;
CREATE TABLE `translates` (
                              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                              `from_code` varchar(8) NOT NULL DEFAULT '',
                              `to_code` varchar(8) NOT NULL DEFAULT '',
                              `from_text` longtext DEFAULT NULL,
                              `to_text` longtext DEFAULT NULL,
                              `create_time` timestamp NULL DEFAULT NULL,
                              PRIMARY KEY (`id`),
                              KEY `from_code_to_code` (`from_code`,`to_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- 2022-05-01 18:18:47