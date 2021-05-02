CREATE TABLE `regions`
(
    `id`        int(11) unsigned NOT NULL AUTO_INCREMENT,
    `title`     varchar(200)     NOT NULL DEFAULT '',
    `parent_id` int(11) unsigned          DEFAULT NULL,
    `url`       varchar(200)     NOT NULL DEFAULT '',
    `donor_url`   varchar(500)     NOT NULL DEFAULT '',
    `icon_url`    varchar(1024)    NOT NULL DEFAULT '',
    `icon_text`   text                      DEFAULT NULL,
    `create_time` timestamp        NULL     DEFAULT NULL,
    `level`     int(10) unsigned NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    UNIQUE KEY `url` (`url`),
    KEY `title` (`title`),
    KEY `parent_id` (`parent_id`),
    CONSTRAINT `regions_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE `categories`
(
    `id`          int(11) unsigned NOT NULL AUTO_INCREMENT,
    `parent_id`   int(11) unsigned          DEFAULT NULL,
    `level`       int(11) unsigned NOT NULL DEFAULT 1,
    `title`       varchar(255)     NOT NULL DEFAULT '',
    `url`         varchar(255)     NOT NULL DEFAULT '',
    `donor_url`   varchar(500)     NOT NULL DEFAULT '',
    `icon_url`    varchar(1024)    NOT NULL DEFAULT '',
    `icon_text`   text                      DEFAULT NULL,
    `create_time` timestamp        NULL     DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `url_level` (`url`, `level`),
    KEY `title` (`title`),
    KEY `parent_id` (`parent_id`),
    KEY `url` (`url`),
    KEY `level` (`level`),
    CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE `ads`
(
    `id`    int(11) unsigned NOT NULL AUTO_INCREMENT,
    `url`            varchar(600)                             NOT NULL DEFAULT '',
    `category_id`    int(11) unsigned                                  DEFAULT NULL,
    `region_id`      int(11) unsigned                                  DEFAULT NULL,
    `title`          varchar(1000)                            NOT NULL DEFAULT '',
    `text`           text                                              DEFAULT NULL,
    `address`        varchar(1000)                            NOT NULL DEFAULT '',
    `coordinates`    varchar(1000)                            NOT NULL DEFAULT '',
    `post_time`      timestamp                                NULL     DEFAULT NULL,
    `price`          decimal(10, 2)                           NOT NULL DEFAULT '0',
    `currency`       varchar(20) COLLATE 'utf8mb4_general_ci' NOT NULL DEFAULT '',
    `seller_name`    VARCHAR(100)                             NOT NULL DEFAULT '',
    `seller_postfix` VARCHAR(100)                             NOT NULL DEFAULT '',
    `seller_phone`   VARCHAR(100)                             NOT NULL DEFAULT '',
    `deleted_time`   TIMESTAMP  NULL,
PRIMARY KEY (`id`),
    UNIQUE KEY `url` (`url`),
    KEY `category_id` (`category_id`),
    KEY `region_id` (`region_id`),
    INDEX (`deleted_time`),

CONSTRAINT `ads_ibfk_3` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
    CONSTRAINT `ads_ibfk_4` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE `ads_images`
(
    `id`    int(11) unsigned NOT NULL AUTO_INCREMENT,
    `ad_id` int(11) unsigned DEFAULT NULL,
    `big`   varchar(1000)    NOT NULL DEFAULT '',
    `small` varchar(1000)    NOT NULL DEFAULT '',
    PRIMARY KEY (`id`),
    KEY `ad_id` (`ad_id`),
    CONSTRAINT `ads_images_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE `details_fields`
(
    `id`          int(11) unsigned NOT NULL AUTO_INCREMENT,
    `category_id` int(11) unsigned                DEFAULT NULL,
    `field`       varchar(100)  DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `category_field` (`category_id`, `field`),
    CONSTRAINT `details_fields_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE `ads_details`
(
    `id`               int(11)                 NOT NULL AUTO_INCREMENT,
    `details_field_id` int(11) unsigned                 DEFAULT NULL,
    `ad_id`            int(11) unsigned                 DEFAULT NULL,
    `value`            text  NOT NULL DEFAULT '',
    PRIMARY KEY (`id`),
    KEY `details_field_id` (`details_field_id`),
    KEY `ad_id` (`ad_id`),
    CONSTRAINT `ads_details_ibfk_1` FOREIGN KEY (`details_field_id`) REFERENCES `details_fields` (`id`) ON DELETE CASCADE,
    CONSTRAINT `ads_details_ibfk_2` FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE `complaints` (
                              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                              `email` varchar(200)  NOT NULL DEFAULT '',
                              `message` text ,
                              `ad_id` int(11) unsigned NULL,
                              `response_time` timestamp NULL DEFAULT NULL,
                              `ignore_time` timestamp NULL DEFAULT NULL,
                              `ip` varchar(20)  NOT NULL DEFAULT '',
                              `domain` varchar(30)  NOT NULL DEFAULT '',
                              `page` varchar(100)  NOT NULL DEFAULT '',
                              `create_time` timestamp NULL DEFAULT NULL,
                              PRIMARY KEY (`id`),
                              KEY `create_time` (`create_time`),
                              KEY `ip` (`ip`),
                              KEY `domain` (`domain`),
                              KEY `page` (`page`),
                              FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE CASCADE,
                              KEY `response_time_ignore_time` (`response_time`,`ignore_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;