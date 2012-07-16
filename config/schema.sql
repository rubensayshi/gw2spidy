
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
    `restriction_level` VARCHAR(255) NOT NULL,
    `rarity` VARCHAR(255) NOT NULL,
    `vendor_sell_price` VARCHAR(255) NOT NULL,
    `img` VARCHAR(255) NOT NULL,
    `rarity_word` VARCHAR(255) NOT NULL,
    `item_type_id` INTEGER NOT NULL,
    `item_sub_type_id` INTEGER NOT NULL,
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

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
