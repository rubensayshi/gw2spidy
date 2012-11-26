# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `item`
    ADD `tp_name` VARCHAR(255) NOT NULL AFTER `name`,
    ADD `clean_name` VARCHAR(255) NOT NULL AFTER `tp_name`,
    ADD `clean_tp_name` VARCHAR(255) NOT NULL AFTER `clean_name`;

CREATE INDEX `search_name` ON `item` (`name`,`tp_name`,`clean_name`,`clean_tp_name`);

CREATE INDEX `search_name` ON `recipe` (`name`);

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;