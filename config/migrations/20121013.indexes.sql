<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1350154820.
 * Generated on 2012-10-13 14:00:20 by user
 */
class PropelMigration_1350154820
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

DROP TABLE IF EXISTS `recipe_armorsmith`;

DROP TABLE IF EXISTS `recipe_ingredient_armorsmith`;

DROP INDEX `retrieve_by_date_time` ON `buy_listing`;

CREATE INDEX `retrieve_by_datetime` ON `buy_listing` (`item_id`,`listing_datetime`);

ALTER TABLE `buy_listing` ADD CONSTRAINT `buy_listing_FK_1`
    FOREIGN KEY (`item_id`)
    REFERENCES `item` (`data_id`);

ALTER TABLE `item` ADD CONSTRAINT `item_FK_1`
    FOREIGN KEY (`item_type_id`)
    REFERENCES `item_type` (`id`);

ALTER TABLE `item` ADD CONSTRAINT `item_FK_2`
    FOREIGN KEY (`item_sub_type_id`)
    REFERENCES `item_sub_type` (`id`);

ALTER TABLE `item_sub_type` ADD CONSTRAINT `item_sub_type_FK_1`
    FOREIGN KEY (`main_type_id`)
    REFERENCES `item_type` (`id`);

ALTER TABLE `recipe` ADD CONSTRAINT `recipe_FK_1`
    FOREIGN KEY (`discipline_id`)
    REFERENCES `discipline` (`id`);

ALTER TABLE `recipe` ADD CONSTRAINT `recipe_FK_2`
    FOREIGN KEY (`result_item_id`)
    REFERENCES `item` (`data_id`);

ALTER TABLE `recipe_ingredient` ADD CONSTRAINT `recipe_ingredient_FK_1`
    FOREIGN KEY (`recipe_id`)
    REFERENCES `recipe` (`data_id`);

ALTER TABLE `recipe_ingredient` ADD CONSTRAINT `recipe_ingredient_FK_2`
    FOREIGN KEY (`item_id`)
    REFERENCES `item` (`data_id`);

DROP INDEX `retrieve_by_date_time` ON `sell_listing`;

CREATE INDEX `retrieve_by_datetime` ON `sell_listing` (`item_id`,`listing_datetime`);

ALTER TABLE `sell_listing` ADD CONSTRAINT `sell_listing_FK_1`
    FOREIGN KEY (`item_id`)
    REFERENCES `item` (`data_id`);

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

ALTER TABLE `buy_listing` DROP FOREIGN KEY `buy_listing_FK_1`;

DROP INDEX `retrieve_by_datetime` ON `buy_listing`;

CREATE INDEX `retrieve_by_date_time` ON `buy_listing` (`item_id`);

ALTER TABLE `item` DROP FOREIGN KEY `item_FK_1`;

ALTER TABLE `item` DROP FOREIGN KEY `item_FK_2`;

ALTER TABLE `item_sub_type` DROP FOREIGN KEY `item_sub_type_FK_1`;

ALTER TABLE `recipe` DROP FOREIGN KEY `recipe_FK_1`;

ALTER TABLE `recipe` DROP FOREIGN KEY `recipe_FK_2`;

ALTER TABLE `recipe_ingredient` DROP FOREIGN KEY `recipe_ingredient_FK_1`;

ALTER TABLE `recipe_ingredient` DROP FOREIGN KEY `recipe_ingredient_FK_2`;

ALTER TABLE `sell_listing` DROP FOREIGN KEY `sell_listing_FK_1`;

DROP INDEX `retrieve_by_datetime` ON `sell_listing`;

CREATE INDEX `retrieve_by_date_time` ON `sell_listing` (`item_id`);

CREATE TABLE `recipe_armorsmith`
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
    INDEX `recipe_FI_2` (`result_item_id`)
) ENGINE=MyISAM;

CREATE TABLE `recipe_ingredient_armorsmith`
(
    `recipe_id` INTEGER NOT NULL,
    `item_id` INTEGER NOT NULL,
    `count` INTEGER DEFAULT 1 NOT NULL,
    PRIMARY KEY (`recipe_id`,`item_id`),
    INDEX `recipe_ingredient_FI_2` (`item_id`)
) ENGINE=MyISAM;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
    }

}