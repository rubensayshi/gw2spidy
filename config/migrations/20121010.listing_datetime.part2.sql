# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `buy_listing` DROP `listing_date`;

ALTER TABLE `buy_listing` DROP `listing_time`;

ALTER TABLE `sell_listing` DROP `listing_date`;

ALTER TABLE `sell_listing` DROP `listing_time`;

SET FOREIGN_KEY_CHECKS = 1;