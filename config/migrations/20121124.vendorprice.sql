# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `item`
    ADD `vendor_price` INTEGER NOT NULL AFTER `vendor_sell_price`,
    ADD `karma_price` INTEGER NOT NULL AFTER `vendor_price`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;