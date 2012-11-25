
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- item_type
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `item_type`;

CREATE TABLE `item_type`
(
    `id` INTEGER NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM;

-- ---------------------------------------------------------------------
-- item_sub_type
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `item_sub_type`;

CREATE TABLE `item_sub_type`
(
    `id` INTEGER NOT NULL,
    `main_type_id` INTEGER NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`,`main_type_id`),
    INDEX `item_sub_type_FI_1` (`main_type_id`),
    CONSTRAINT `item_sub_type_FK_1`
        FOREIGN KEY (`main_type_id`)
        REFERENCES `item_type` (`id`)
) ENGINE=MyISAM;

-- ---------------------------------------------------------------------
-- item
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `item`;

CREATE TABLE `item`
(
    `data_id` INTEGER NOT NULL,
    `type_id` INTEGER NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `gem_store_description` VARCHAR(255) NOT NULL,
    `gem_store_blurb` VARCHAR(255) NOT NULL,
    `restriction_level` INTEGER NOT NULL,
    `rarity` INTEGER NOT NULL,
    `vendor_sell_price` INTEGER NOT NULL,
    `vendor_price` INTEGER NOT NULL,
    `karma_price` INTEGER NOT NULL,
    `img` VARCHAR(255) NOT NULL,
    `rarity_word` VARCHAR(255) NOT NULL,
    `item_type_id` INTEGER,
    `item_sub_type_id` INTEGER,
    `max_offer_unit_price` INTEGER NOT NULL,
    `min_sale_unit_price` INTEGER NOT NULL,
    `offer_availability` INTEGER DEFAULT 0 NOT NULL,
    `sale_availability` INTEGER DEFAULT 0 NOT NULL,
    `gw2db_id` INTEGER,
    `gw2db_external_id` INTEGER,
    `last_price_changed` DATETIME,
    `sale_price_change_last_hour` INTEGER DEFAULT 0,
    `offer_price_change_last_hour` INTEGER DEFAULT 0,
    PRIMARY KEY (`data_id`),
    INDEX `item_FI_1` (`item_type_id`),
    INDEX `item_FI_2` (`item_sub_type_id`),
    CONSTRAINT `item_FK_1`
        FOREIGN KEY (`item_type_id`)
        REFERENCES `item_type` (`id`),
    CONSTRAINT `item_FK_2`
        FOREIGN KEY (`item_sub_type_id`)
        REFERENCES `item_sub_type` (`id`)
) ENGINE=MyISAM;

-- ---------------------------------------------------------------------
-- gw2db_item_archive
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `gw2db_item_archive`;

CREATE TABLE `gw2db_item_archive`
(
    `ID` INTEGER NOT NULL,
    `ExternalID` INTEGER,
    `DataID` INTEGER,
    `Name` VARCHAR(255),
    PRIMARY KEY (`ID`)
) ENGINE=MyISAM;

-- ---------------------------------------------------------------------
-- discipline
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `discipline`;

CREATE TABLE `discipline`
(
    `id` INTEGER NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM;

-- ---------------------------------------------------------------------
-- recipe
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `recipe`;

CREATE TABLE `recipe`
(
    `data_id` INTEGER NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `discipline_id` INTEGER,
    `rating` INTEGER(4) DEFAULT 0,
    `result_item_id` INTEGER,
    `count` INTEGER(4) DEFAULT 1,
    `cost` INTEGER,
    `sell_price` INTEGER,
    `profit` INTEGER,
    `updated` DATETIME,
    `gw2db_id` INTEGER,
    `gw2db_external_id` INTEGER,
    PRIMARY KEY (`data_id`),
    INDEX `recipe_FI_1` (`discipline_id`),
    INDEX `recipe_FI_2` (`result_item_id`),
    CONSTRAINT `recipe_FK_1`
        FOREIGN KEY (`discipline_id`)
        REFERENCES `discipline` (`id`),
    CONSTRAINT `recipe_FK_2`
        FOREIGN KEY (`result_item_id`)
        REFERENCES `item` (`data_id`)
) ENGINE=MyISAM;

-- ---------------------------------------------------------------------
-- recipe_ingredient
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `recipe_ingredient`;

CREATE TABLE `recipe_ingredient`
(
    `recipe_id` INTEGER NOT NULL,
    `item_id` INTEGER NOT NULL,
    `count` INTEGER DEFAULT 1 NOT NULL,
    PRIMARY KEY (`recipe_id`,`item_id`),
    INDEX `recipe_ingredient_FI_2` (`item_id`),
    CONSTRAINT `recipe_ingredient_FK_1`
        FOREIGN KEY (`recipe_id`)
        REFERENCES `recipe` (`data_id`),
    CONSTRAINT `recipe_ingredient_FK_2`
        FOREIGN KEY (`item_id`)
        REFERENCES `item` (`data_id`)
) ENGINE=MyISAM;

-- ---------------------------------------------------------------------
-- sell_listing
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `sell_listing`;

CREATE TABLE `sell_listing`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `listing_datetime` DATETIME NOT NULL,
    `item_id` INTEGER NOT NULL,
    `listings` INTEGER NOT NULL,
    `unit_price` INTEGER NOT NULL,
    `quantity` INTEGER NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `retrieve_by_datetime` (`item_id`, `listing_datetime`),
    CONSTRAINT `sell_listing_FK_1`
        FOREIGN KEY (`item_id`)
        REFERENCES `item` (`data_id`)
) ENGINE=MyISAM;

-- ---------------------------------------------------------------------
-- buy_listing
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `buy_listing`;

CREATE TABLE `buy_listing`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `listing_datetime` DATETIME NOT NULL,
    `item_id` INTEGER NOT NULL,
    `listings` INTEGER NOT NULL,
    `unit_price` INTEGER NOT NULL,
    `quantity` INTEGER NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `retrieve_by_datetime` (`item_id`, `listing_datetime`),
    CONSTRAINT `buy_listing_FK_1`
        FOREIGN KEY (`item_id`)
        REFERENCES `item` (`data_id`)
) ENGINE=MyISAM;

-- ---------------------------------------------------------------------
-- gold_to_gem_rate
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `gold_to_gem_rate`;

CREATE TABLE `gold_to_gem_rate`
(
    `rate_datetime` DATETIME NOT NULL,
    `rate` INTEGER NOT NULL,
    `volume` BIGINT NOT NULL,
    PRIMARY KEY (`rate_datetime`)
) ENGINE=MyISAM;

-- ---------------------------------------------------------------------
-- gem_to_gold_rate
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `gem_to_gold_rate`;

CREATE TABLE `gem_to_gold_rate`
(
    `rate_datetime` DATETIME NOT NULL,
    `rate` INTEGER NOT NULL,
    `volume` BIGINT NOT NULL,
    PRIMARY KEY (`rate_datetime`)
) ENGINE=MyISAM;

-- ---------------------------------------------------------------------
-- gw2session
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `gw2session`;

CREATE TABLE `gw2session`
(
    `session_key` VARCHAR(255) NOT NULL,
    `game_session` TINYINT(1) NOT NULL,
    `created` DATETIME NOT NULL,
    `source` VARCHAR(255),
    PRIMARY KEY (`session_key`)
) ENGINE=MyISAM;

-- ---------------------------------------------------------------------
-- user
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255),
    `roles` VARCHAR(255) DEFAULT 'USER_ROLE',
    `hybrid_auth_provider_id` VARCHAR(50),
    `hybrid_auth_id` VARCHAR(255),
    PRIMARY KEY (`id`),
    UNIQUE INDEX `unique_username` (`username`)
) ENGINE=MyISAM;

-- ---------------------------------------------------------------------
-- watchlist
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `watchlist`;

CREATE TABLE `watchlist`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `user_id` INTEGER NOT NULL,
    `item_id` INTEGER NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `unique_user_item` (`user_id`, `item_id`),
    INDEX `watchlist_FI_2` (`item_id`),
    CONSTRAINT `watchlist_FK_1`
        FOREIGN KEY (`user_id`)
        REFERENCES `user` (`id`),
    CONSTRAINT `watchlist_FK_2`
        FOREIGN KEY (`item_id`)
        REFERENCES `item` (`data_id`)
) ENGINE=MyISAM;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
