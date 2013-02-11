# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `recipe`
    ADD `requires_unlock` INTEGER DEFAULT 0 NOT NULL AFTER `updated`;

ALTER TABLE `item`
    ADD `last_updated` DATETIME AFTER `last_price_changed`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;