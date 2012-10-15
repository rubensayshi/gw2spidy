# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE `watchlist`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `user_id` INTEGER NOT NULL,
    `item_id` INTEGER NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `watchlist_FI_1` (`user_id`),
    INDEX `watchlist_FI_2` (`item_id`),
    CONSTRAINT `watchlist_FK_1`
        FOREIGN KEY (`user_id`)
        REFERENCES `user` (`id`),
    CONSTRAINT `watchlist_FK_2`
        FOREIGN KEY (`item_id`)
        REFERENCES `item` (`data_id`)
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
