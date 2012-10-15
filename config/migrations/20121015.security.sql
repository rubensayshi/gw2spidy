# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE `user`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `roles` VARCHAR(255) DEFAULT '',
    `email` VARCHAR(255) NOT NULL AFTER `username`,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM;

CREATE UNIQUE INDEX `unique_username` ON `user` (`username`);
CREATE UNIQUE INDEX `unique_email` ON `user` (`email`);

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
