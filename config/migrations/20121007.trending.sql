# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `item`
    ADD `last_price_changed` DATETIME AFTER `gw2db_external_id`,
    ADD `sale_price_change_last_hour` INTEGER DEFAULT 0 AFTER `last_price_changed`,
    ADD `offer_price_change_last_hour` INTEGER DEFAULT 0 AFTER `sale_price_change_last_hour`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;