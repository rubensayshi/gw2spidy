<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1349268201.
 * Generated on 2012-10-03 14:43:21 by ruben
 */
class PropelMigration_1349268201
{

    public function preUp($manager)
    {
        // add the pre-migration code here
    }

    public function postUp($manager)
    {
        // add the post-migration code here
    }

    public function preDown($manager)
    {
        // add the pre-migration code here
    }

    public function postDown($manager)
    {
        // add the post-migration code here
    }

    /**
     * Get the SQL statements for the Up migration
     *
     * @return array list of the SQL strings to execute for the Up migration
     *               the keys being the datasources
     */
    public function getUpSQL()
    {
        return array (
  'gw2spidy' => '
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `buy_listing` ADD CONSTRAINT `buy_listing_FK_1`
    FOREIGN KEY (`item_id`)
    REFERENCES `item` (`data_id`);

ALTER TABLE `item` CHANGE `item_type_id` `item_type_id` INTEGER;

ALTER TABLE `item` CHANGE `item_sub_type_id` `item_sub_type_id` INTEGER;

ALTER TABLE `item` ADD CONSTRAINT `item_FK_1`
    FOREIGN KEY (`item_type_id`)
    REFERENCES `item_type` (`id`);

ALTER TABLE `item` ADD CONSTRAINT `item_FK_2`
    FOREIGN KEY (`item_sub_type_id`)
    REFERENCES `item_sub_type` (`id`);

ALTER TABLE `item_sub_type` ADD CONSTRAINT `item_sub_type_FK_1`
    FOREIGN KEY (`main_type_id`)
    REFERENCES `item_type` (`id`);

ALTER TABLE `sell_listing` ADD CONSTRAINT `sell_listing_FK_1`
    FOREIGN KEY (`item_id`)
    REFERENCES `item` (`data_id`);

CREATE TABLE `gw2db_item_archive`
(
    `ID` INTEGER NOT NULL,
    `ExternalID` INTEGER,
    `DataID` INTEGER,
    `Name` VARCHAR(255),
    PRIMARY KEY (`ID`)
) ENGINE=MyISAM;

CREATE TABLE `discipline`
(
    `id` INTEGER NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM;

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

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
    }

    /**
     * Get the SQL statements for the Down migration
     *
     * @return array list of the SQL strings to execute for the Down migration
     *               the keys being the datasources
     */
    public function getDownSQL()
    {
        return array (
  'gw2spidy' => '
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `gw2db_item_archive`;

DROP TABLE IF EXISTS `discipline`;

DROP TABLE IF EXISTS `recipe`;

DROP TABLE IF EXISTS `recipe_ingredient`;

ALTER TABLE `buy_listing` DROP FOREIGN KEY `buy_listing_FK_1`;

ALTER TABLE `item` DROP FOREIGN KEY `item_FK_1`;

ALTER TABLE `item` DROP FOREIGN KEY `item_FK_2`;

ALTER TABLE `item` CHANGE `item_type_id` `item_type_id` INTEGER NOT NULL;

ALTER TABLE `item` CHANGE `item_sub_type_id` `item_sub_type_id` INTEGER NOT NULL;

ALTER TABLE `item_sub_type` DROP FOREIGN KEY `item_sub_type_FK_1`;

ALTER TABLE `sell_listing` DROP FOREIGN KEY `sell_listing_FK_1`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
    }

}