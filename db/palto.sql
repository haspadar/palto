-- Adminer 4.8.1 MySQL 10.3.34-MariaDB-0ubuntu0.20.04.1 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

CREATE TABLE `phinxlog` (
                            `version` bigint(20) NOT NULL,
                            `migration_name` varchar(100) DEFAULT NULL,
                            `start_time` timestamp NULL DEFAULT NULL,
                            `end_time` timestamp NULL DEFAULT NULL,
                            `breakpoint` tinyint(1) NOT NULL DEFAULT 0,
                            PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `regions` (
                           `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                           `title` varchar(200) NOT NULL DEFAULT '',
                           `abbreviation` varchar(10) NOT NULL DEFAULT '',
                           `parent_id` int(11) unsigned DEFAULT NULL,
                           `url` varchar(200) NOT NULL DEFAULT '',
                           `donor_url` varchar(500) NOT NULL DEFAULT '',
                           `icon_url` varchar(1024) NOT NULL DEFAULT '',
                           `icon_text` text DEFAULT NULL,
                           `create_time` timestamp NULL DEFAULT NULL,
                           `left_id` int(10) unsigned NOT NULL DEFAULT 0,
                           `right_id` int(10) unsigned NOT NULL DEFAULT 0,
                           `level` int(10) unsigned NOT NULL DEFAULT 1,
                           `tree_id` int(10) unsigned DEFAULT 1,
                           `ads_count` int(10) unsigned NOT NULL DEFAULT 0,
                           PRIMARY KEY (`id`),
                           UNIQUE KEY `url` (`url`),
                           KEY `title` (`title`),
                           KEY `parent_id` (`parent_id`),
                           KEY `tree_id` (`tree_id`),
                           CONSTRAINT `regions_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
                              `left_id` int(10) unsigned NOT NULL DEFAULT 0,
                              `right_id` int(10) unsigned NOT NULL DEFAULT 0,
                              `ads_count` int(10) unsigned NOT NULL DEFAULT 0,
                              PRIMARY KEY (`id`),
                              UNIQUE KEY `url_level` (`url`,`level`),
                              KEY `title` (`title`),
                              KEY `parent_id` (`parent_id`),
                              KEY `url` (`url`),
                              KEY `level` (`level`),
                              KEY `tree_id` (`tree_id`),
                              CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


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
  `synonym_id` int(10) unsigned DEFAULT NULL,
  `field` enum('title','text') DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT NULL,
  `region_level_4_id` int(11) unsigned DEFAULT NULL,
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
  KEY `synonym_id` (`synonym_id`),
  KEY `field` (`field`),
  KEY `region_level_4_id` (`region_level_4_id`),
  CONSTRAINT `ads_ibfk_10` FOREIGN KEY (`region_level_3_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ads_ibfk_11` FOREIGN KEY (`synonym_id`) REFERENCES `synonyms` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ads_ibfk_12` FOREIGN KEY (`region_level_4_id`) REFERENCES `regions` (`id`),
  CONSTRAINT `ads_ibfk_3` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ads_ibfk_4` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ads_ibfk_5` FOREIGN KEY (`category_level_3_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ads_ibfk_6` FOREIGN KEY (`category_level_2_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ads_ibfk_7` FOREIGN KEY (`category_level_1_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ads_ibfk_8` FOREIGN KEY (`region_level_2_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ads_ibfk_9` FOREIGN KEY (`region_level_1_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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


CREATE TABLE `ads_images` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ad_id` int(11) unsigned DEFAULT NULL,
  `big` varchar(1000) NOT NULL DEFAULT '',
  `small` varchar(1000) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `ad_id` (`ad_id`),
  CONSTRAINT `ads_images_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `details_fields` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(11) unsigned DEFAULT NULL,
  `field` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_field` (`category_id`,`field`),
  CONSTRAINT `details_fields_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `live` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(11) unsigned DEFAULT NULL,
  `region_id` int(11) unsigned DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_id_region_id` (`category_id`,`region_id`),
  KEY `region_id` (`region_id`),
  KEY `create_time` (`create_time`),
  CONSTRAINT `live_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `live_ibfk_2` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `templates` (
                             `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                             `name` varchar(50) NOT NULL DEFAULT '',
                             PRIMARY KEY (`id`),
                             UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `translates`;
CREATE TABLE `translates` (
                              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                              `name` varchar(600) NOT NULL DEFAULT '',
                              `value` text DEFAULT NULL,
                              PRIMARY KEY (`id`),
                              UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `comment` varchar(255) NOT NULL DEFAULT '',
  `template_id` int(10) unsigned DEFAULT NULL,
  `url` varchar(255) NOT NULL DEFAULT '',
  `function` varchar(255) NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text DEFAULT NULL,
  `h1` varchar(255) NOT NULL DEFAULT '',
  `h2` varchar(255) NOT NULL DEFAULT '',
  `priority` int(10) unsigned NOT NULL DEFAULT 0,
  `router_priority` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `template_id` (`template_id`),
  KEY `priority` (`priority`),
  KEY `router_priority` (`router_priority`),
  CONSTRAINT `pages_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `templates` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `comment` varchar(255) NOT NULL DEFAULT '',
  `value` text DEFAULT NULL,
  `group` varchar(50) NOT NULL DEFAULT '',
  `type` varchar(50) NOT NULL DEFAULT 'text',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `category` (`group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `synonyms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `title_category_id` (`title`,`category_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `synonyms_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `yandex_translates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `from_code` varchar(8) NOT NULL DEFAULT '',
  `to_code` varchar(8) NOT NULL DEFAULT '',
  `from_text` longtext DEFAULT NULL,
  `to_text` longtext DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `from_code_to_code` (`from_code`,`to_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `pages` (`id`, `name`, `comment`, `template_id`, `url`, `function`, `is_enabled`, `title`, `description`, `h1`, `h2`, `priority`, `router_priority`) VALUES
                                                                                                                                                                     (1,	'main',	'Главная',	2,	'/',	'showIndex',	1,	'Pets for Sale or Adoption - Free Local Pet Classified Ads in US',	'Free classified ads for pets and pet supplies in US cities.',	'Pets Classifieds in USA',	'',	10,	10),
                                                                                                                                                                     (2,	'registration',	'Регистрация',	7,	'/registration',	'showRegistration',	1,	'Registration on Petsus.net',	'Register to create a posting on Petsus.net',	'Registration',	'',	20,	20),
                                                                                                                                                                     (4,	'regions',	'Все регионы',	6,	'/regions',	'showRegionsList',	1,	'Pets Classifieds: Regions',	'Regions of Pet Classified Ads in USA',	'All Regions',	'',	40,	40),
                                                                                                                                                                     (5,	'categories',	'Все категории',	5,	'/categories',	'showCategoriesList',	1,	'Pets Classifieds: Categories',	'Categories of Pet Classified Ads in USA',	'All Categories',	'',	30,	30),
                                                                                                                                                                     (6,	'ad',	'Карточка',	4,	'/([a-zA-Z0-9_-]+)/([a-zA-Z0-9_-]+)(/[a-zA-Z0-9_-]+)?(/[a-zA-Z0-9_-]+)?/ad(\\d+)',	'showAd',	1,	':AD in :REGION, :REGION_1_ABBREVIATION - :CATEGORY_2 for sale or adoption from craigslist',	'Categories of Classified Ads in Washington',	':AD in :REGION, :REGION_1_ABBREVIATION near me: :CATEGORY_1 classifieds',	'',	170,	50),
                                                                                                                                                                     (8,	'region_0_category_1',	'Регион 0-го уровня, категория 1-го уровня',	3,	'/([a-zA-Z0-9_-]+)/([a-zA-Z0-9_-]+)(/[a-zA-Z0-9_-]+)?(/[a-zA-Z0-9_-]+)?(/d+)?',	'showCategory',	1,	':CATEGORY_IN_REGION for sale, for adoption: classified ads from craigslist',	'Buy or adopt :CATEGORIES in all states and cities of USA',	':CATEGORY_IN_REGION near me: pets classifieds',	'',	60,	70),
                                                                                                                                                                     (11,	'404_ad',	'404 для ненайденного объявления',	8,	'',	'showNotFound',	1,	'404',	'404',	'Ad was deleted',	'Categories',	180,	0),
                                                                                                                                                                     (12,	'404_default',	'404 по умолчанию',	8,	'',	'showNotFound',	1,	'404',	'404',	'Not found',	'Categories',	190,	0),
                                                                                                                                                                     (19,	'region_0_category_2',	'Регион 0-го уровня, категория 2-го уровня',	3,	'/([a-zA-Z0-9_-]+)/([a-zA-Z0-9_-]+)(/[a-zA-Z0-9_-]+)?(/[a-zA-Z0-9_-]+)?(/d+)?',	'showCategory',	1,	':CATEGORY_2 in :REGION for sale, for adoption: classified ads from craigslist',	'Buy or adopt :CATEGORY_2 in all states and cities of USA',	':CATEGORY_2 in :REGION near me: :CATEGORY_1 classifieds',	'',	70,	70),
                                                                                                                                                                     (20,	'region_1_category_1',	'Регион 1-го уровня, категория 1-го уровня',	3,	'/([a-zA-Z0-9_-]+)/([a-zA-Z0-9_-]+)(/[a-zA-Z0-9_-]+)?(/[a-zA-Z0-9_-]+)?(/d+)?',	'showCategory',	1,	':CATEGORY_1 in :REGION_1 for adoption, for sale: classified ads from craigslist',	'Free classified ads for :CATEGORY_1 and pet supplies in :REGION_1.',	':CATEGORY_1 in :REGION_1 near me: classifieds',	'',	90,	70),
                                                                                                                                                                     (21,	'region_1_category_2',	'Регион 1-го уровня, категория 2-го уровня',	3,	'/([a-zA-Z0-9_-]+)/([a-zA-Z0-9_-]+)(/[a-zA-Z0-9_-]+)?(/[a-zA-Z0-9_-]+)?(/d+)?',	'showCategory',	1,	':CATEGORY_2 in :REGION_1 for adoption, for sale: classified ads from craigslist',	'Free classified ads for :CATEGORY_2 and :CATEGORY_1 supplies in :REGION_1.',	':CATEGORY_2 in :REGION_1 near me: :CATEGORY_1 classifieds',	'',	100,	70),
                                                                                                                                                                     (22,	'region_2_category_1',	'Регион 2-го уровня, категория 1-го уровня',	3,	'/([a-zA-Z0-9_-]+)/([a-zA-Z0-9_-]+)(/[a-zA-Z0-9_-]+)?(/[a-zA-Z0-9_-]+)?(/d+)?',	'showCategory',	1,	':CATEGORY_1 in :REGION_2, :REGION_1_ABBREVIATION for sale, for adoption: classifieds from craigslist',	'Free classified ads for :CATEGORY_1 and supplies in :REGION_2, :REGION_1. Buy or adopt dogs on Petsus.net!',	':CATEGORY_1 in :REGION_2, :REGION_1_ABBREVIATION near me: classified ads',	'',	130,	70),
                                                                                                                                                                     (23,	'region_2_category_2',	'Регион 2-го уровня, категория 2-го уровня',	3,	'/([a-zA-Z0-9_-]+)/([a-zA-Z0-9_-]+)(/[a-zA-Z0-9_-]+)?(/[a-zA-Z0-9_-]+)?(/d+)?',	'showCategory',	1,	':CATEGORY_2 in :REGION_2, :REGION_1_ABBREVIATION for sale, for adoption: classified ads from craigslist',	'Free classified ads for :CATEGORY_2 and supplies for :CATEGORY_1 in Los :REGION_2, :REGION_1. Buy or adopt :CATEGORY_2 :CATEGORY_1 on Petsus.net!',	':CATEGORY_2 in :REGION_2, :REGION_1_ABBREVIATION near me: :CATEGORY_1 classifieds',	'',	140,	70),
                                                                                                                                                                     (24,	'region_3_category_1',	'Регион 3-го уровня, категория 1-го уровня',	3,	'/([a-zA-Z0-9_-]+)/([a-zA-Z0-9_-]+)(/[a-zA-Z0-9_-]+)?(/[a-zA-Z0-9_-]+)?(/d+)?',	'showCategory',	1,	':CATEGORY_1 in :REGION_3, :REGION_2, :REGION_1_ABBREVIATION for adoption and sale: classified ads from craigslist',	'Free classifieds for :CATEGORY_1 in :REGION_3, :REGION_1. Search a pet for sale or adoption.',	':CATEGORY_1 in :REGION_3, :REGION_1_ABBREVIATION near me: free classifieds',	'',	160,	70),
                                                                                                                                                                     (25,	'region_3_category_2',	'Регион 3-го уровня, категория 2-го уровня',	3,	'/([a-zA-Z0-9_-]+)/([a-zA-Z0-9_-]+)(/[a-zA-Z0-9_-]+)?(/[a-zA-Z0-9_-]+)?(/d+)?',	'showCategory',	1,	':CATEGORY_2 in :REGION_3, :REGION_2, :REGION_1_ABBREVIATION for adoption and sale: classifieds from craigslist',	'Free classifieds for :CATEGORY_2 in :REGION_3, :REGION_1. Buy or adopt :CATEGORY_2 :CATEGORY_1 on Petsus.net!',	':CATEGORY_2 in :REGION_3, :REGION_1_ABBREVIATION near me: :CATEGORY_1 classified ads',	'',	160,	70),
                                                                                                                                                                     (26,	'region_0',	'Регион 0-го уровня',	3,	'/([a-zA-Z0-9_-]+)(/d+)?',	'showRegion',	1,	'Pet Classified Ads in :REGION_0',	'Pets for sale or adoption in all states and cities of :REGION_0',	'Pets in :REGION_0 near me',	'',	50,	60),
                                                                                                                                                                     (27,	'region_1',	'Регион 1-го уровня',	3,	'/([a-zA-Z0-9_-]+)(/d+)?',	'showRegion',	1,	'Pets in :REGION_1 for sale, for adoption: classified ads from craigslist',	'Free classified ads for pets and pet supplies in :REGION_1.',	'Pets in :REGION_1 near me: classifieds',	'',	80,	60),
                                                                                                                                                                     (28,	'region_2',	'Регион 2-го уровня',	3,	'/([a-zA-Z0-9_-]+)(/d+)?',	'showRegion',	1,	'Pets in :REGION_2, :REGION_1_ABBREVIATION for sale, for adoption: classified ads from craigslist',	'Free classifieds for pets in :REGION_2, :REGION_1. Search a pet for sale or adoption.',	'Pets in :REGION_2, :REGION_1_ABBREVIATION: classifieds',	'',	110,	60),
                                                                                                                                                                     (29,	'region_3',	'Регион 3-го уровня',	3,	'/([a-zA-Z0-9_-]+)(/d+)?',	'showRegion',	1,	'Pets in :REGION_3, :REGION_2, :REGION_1_ABBREVIATION for adoption and sale: classified ads from craigslist',	'Free classifieds for pets in :REGION_3, :REGION_1. Search a pet for sale or adoption.',	'Pets in :REGION_3, :REGION_1_ABBREVIATION near me: classifieds',	'',	150,	60);

INSERT INTO `settings` (`id`, `name`, `comment`, `value`, `group`, `type`) VALUES
                                                                               (1,	'template_theme',	'Тема для шаблонов',	'laspot-div',	'Шаблоны',	'theme'),
                                                                               (2,	'categories_parser',	'Парсер категорий',	'craigslist/dallasads.php',	'Парсеры',	'categories_parser'),
                                                                               (3,	'ads_parser',	'Парсер объявлений',	'craigslist/pets.php',	'Парсеры',	'ads_parser'),
                                                                               (4,	'is_karman_panel_enabled',	'Показывать панель',	'0',	'Карман',	'bool'),
                                                                               (72,	'sunduk_url',	'Ссылка для бэкапов',	'',	'Реквизиты',	'url'),
                                                                               (73,	'smtp_email',	'Исходящая почта',	'',	'Реквизиты',	'email'),
                                                                               (74,	'smtp_password',	'Пароль для исходящей почты',	'',	'Реквизиты',	'password'),
                                                                               (75,	'smtp_from',	'Имя отправителя для исходящей почты',	'Support',	'Реквизиты',	'string'),
                                                                               (76,	'smtp_host',	'Хост для исходящей почты',	'smtp.yandex.ru',	'Реквизиты',	'host'),
                                                                               (77,	'smtp_port',	'Порт для исходящей почты',	'465',	'Реквизиты',	'int'),
                                                                               (78,	'smtp_encryption',	'Шифрование для исходящей почты',	'ssl',	'Реквизиты',	'string'),
                                                                               (79,	'yandex_translate_api_key',	'Ключ для Yandex Translate API',	'',	'Реквизиты',	'string'),
                                                                               (80,	'is_auth_enabled',	'Аутентификация включена',	'0',	'Реквизиты',	'bool'),
                                                                               (81,	'auth_login',	'Логин для аутентификации',	'palto',	'Реквизиты',	'string'),
                                                                               (82,	'auth_password',	'Пароль для аутентификации',	'palto25',	'Реквизиты',	'password'),
                                                                               (83,	'default_region_title',	'Название региона по умолчанию',	'USA',	'Шаблоны',	'string'),
                                                                               (84,	'default_region_url',	'Урл региона по умолчанию',	'us',	'Шаблоны',	'string'),
                                                                               (85,	'is_donor_url_enabled',	'Показывать ссылку на донора',	'1',	'Шаблоны',	'bool'),
                                                                               (86,	'is_youtube_url_enabled',	'Показывать YouTube-ролик',	'1',	'Шаблоны',	'bool'),
                                                                               (87,	'is_hot_template_enabled',	'Показывать шаблон Hot',	'1',	'Шаблоны',	'bool'),
                                                                               (88,	'hot_layout_regions',	'Количество регионов в шаблоне Hot',	'17',	'Шаблоны',	'int'),
                                                                               (89,	'hot_layout_categories_level_1',	'Количество категорий 1-го уровня в шаблоне Hot',	'10',	'Шаблоны',	'int'),
                                                                               (90,	'hot_layout_categories_level_2',	'Количество категорий 2-го уровня в шаблоне Hot',	'6',	'Шаблоны',	'int'),
                                                                               (91,	'hot_layout_hot_category',	'Id категории для шаблона Hot',	'971',	'Шаблоны',	'int'),
                                                                               (92,	'hot_layout_hot_ads',	'Количество горячих объявлений в шаблоне Hot',	'5',	'Шаблоны',	'int'),
                                                                               (93,	'hot_layout_new_ads',	'Количество новых объявлений в шаблоне Hot',	'5',	'Шаблоны',	'int'),
                                                                               (94,	'liveinternet',	'Счётчик liveinternet',	'',	'Коды',	'code'),
                                                                               (95,	'google_header',	'Счётчик google для хедера',	'',	'Коды',	'code'),
                                                                               (96,	'google',	'Счётчик google',	'',	'Коды',	'code'),
                                                                               (97,	'adx',	'Счётчики AdExchange',	'',	'Коды',	'codes'),
                                                                               (98,	'google_auto',	'Счётчик google_auto',	'',	'Коды',	'code'),
                                                                               (99,	'google_search',	'Поисковая строка',	'',	'Коды',	'code');

INSERT INTO `templates` (`id`, `name`) VALUES
                                           (8,	'404.php'),
                                           (4,	'ad.php'),
                                           (5,	'categories-list.php'),
                                           (2,	'hot.php'),
                                           (1,	'index.php'),
                                           (3,	'list.php'),
                                           (6,	'regions-list.php'),
                                           (7,	'registration.php');

INSERT INTO `translates` (`id`, `name`, `value`) VALUES
                                                     (1,	'html_lang',	'en-US'),
                                                     (2,	'logo_alt',	'Pets in USA - free classifieds'),
                                                     (3,	'Добавить объявление',	'Create a posting'),
                                                     (4,	'footer_text',	'<a href=\"https://www.petsus.net\" class=\"footer\">Pets in USA</a> - free classifieds'),
                                                     (5,	'cookie_text',	'This website uses cookies in order to offer you the most relevant information. Please accept cookies for optimal performance.'),
                                                     (6,	'СОГЛАСЕН',	'ACCEPT'),
                                                     (7,	'Следующая',	'Next'),
                                                     (8,	'Предыдущая',	'Previous'),
                                                     (9,	'404_h1_ad',	'Ad was deleted'),
                                                     (10,	'404_h1_list',	'Not found'),
                                                     (11,	'404_h2',	'Categories'),
                                                     (12,	'ad_title',	':AD: :CATEGORIES'),
                                                     (13,	'ad_h1',	':AD <span style=\"color:#999\"> in :ADDRESS_WITH_REGION from craigslist</span>'),
                                                     (14,	'Показать телефон',	'Show Phone'),
                                                     (15,	'Нет телефона',	'No Phone'),
                                                     (16,	'Связаться',	'Reply'),
                                                     (17,	'Пожаловаться на объявление',	'Report this ad'),
                                                     (18,	'Жалоба',	'Report'),
                                                     (19,	'Ваша жалоба успешно отправлена.',	'Your report has been sent.'),
                                                     (20,	'Похожие объявления',	'Similar ads'),
                                                     (21,	'Регион',	'Region'),
                                                     (22,	'Время публикации',	'Post time'),
                                                     (23,	'index_title',	'Pets for Sale or Adoption - Free, Local Pet Classified Ads in US'),
                                                     (24,	'index_h1',	'Categories'),
                                                     (25,	'index_description',	'Free classified ads for pets and pet supplies in US cities.'),
                                                     (26,	'hot_h1',	'Pets Classifieds in USA'),
                                                     (27,	'Горячие объявления',	'Hot Ads'),
                                                     (28,	'Новые объявления',	'New Ads'),
                                                     (29,	'Категории',	'Categories'),
                                                     (30,	'categories_title',	'Categories'),
                                                     (31,	'categories_description',	'Categories of Classified Ads in Washington'),
                                                     (32,	'categories_h1',	'Categories'),
                                                     (33,	'regions_title',	'Washington Regions'),
                                                     (34,	'regions_description',	'Regions'),
                                                     (35,	'regions_h1',	'Regions'),
                                                     (36,	'Штаты',	'States'),
                                                     (37,	'Города',	'Cities'),
                                                     (38,	'Районы',	'Regions'),
                                                     (39,	'region_title',	'Pets in :REGION (:REGION_ABBREVIATION) for sale, for adoption: classified ads from craigslist'),
                                                     (40,	'region_description',	'Free classifieds for pets in :REGION. Search a pet for sale or adoption.'),
                                                     (41,	'list_title',	':CATEGORIES in :REGION (:REGION_ABBREVIATION) for sale, for adoption: classified ads from craigslist'),
                                                     (42,	'list_description',	'Free classifieds for :CATEGORIES in :REGION. Search :CATEGORIES for sale or adoption.'),
                                                     (43,	'list_h1',	':CATEGORY_IN_REGION: ads from craigslist'),
                                                     (44,	'в',	'in'),
                                                     (45,	'registration_title',	'Registration'),
                                                     (46,	'registration_description',	'Registration'),
                                                     (47,	'registration_h1',	'Registration'),
                                                     (48,	'Зарегистрировать',	'Create account'),
                                                     (49,	'Забыли пароль?',	'Forgot password?'),
                                                     (50,	'Войти',	'Log in'),
                                                     (51,	'Регистрация',	'Create an account'),
                                                     (52,	'Авторизация',	'Authorization'),
                                                     (53,	'или',	'or');
-- 2022-07-08 07:31:57
